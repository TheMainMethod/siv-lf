drop database if exists `siv-lf`;
create database `siv-lf`;

use `siv-lf`;

create table `productos`
(
id_producto int not null unique auto_increment, 
nombre varchar(50) not null,
descripcion varchar(150),
precio_unitario decimal(15,2) not null,
cantidad int not null,

primary key (id_producto)
);

create table `empleados`
(
id_empleado int not null unique auto_increment,
rfc_empleado char(14) unique,
nombre varchar(50),
apellido_pat varchar(50),
apellido_mat varchar(50),
rol enum('dueño','registro_ventas','registro_inventario','registro_ambos','ninguno') not null default 'ninguno',
correo_electronico varchar(75),
es_turno_matutino boolean not null,
preferencias_interfaz boolean not null default false, -- una ruta a un archivo de configuraciones, que lleva el rfc del trabajador codificado.
-- la idea es que consulte el archivo solo si es 'true'
n_usuario char(20) not null unique,
contra char(255) not null unique, -- se guarda el valor codificado, no la contraseña per se

primary key (id_empleado)
);

create table `teléfonos`
(
id_telefono int not null unique auto_increment,
empleados_id_empleado int not null unique,
telefono decimal(12),

primary key (id_telefono),
constraint foreign key (empleados_id_empleado) references `empleados`(id_empleado) on update cascade on delete cascade
);

create table `entradas`
(
id_entrada int not null unique auto_increment,
empleados_id_empleado int not null,

primary key (id_entrada),
constraint foreign key (empleados_id_empleado) references `empleados`(id_empleado) on update cascade on delete cascade
);

create table `ventas`
(
id_venta int not null unique auto_increment,
monto_total decimal(15,2) not null default 0.00, -- sale casi sobrando, pero también podría consultarse para evitar realizar muchas operaciones
empleados_id_empleado int not null,

primary key (id_venta),
constraint foreign key (empleados_id_empleado) references `empleados`(id_empleado) on update cascade on delete cascade
);

create table `eliminados`
(
id_eliminado int not null unique auto_increment,
empleados_id_empleado int not null,

primary key (id_eliminado),
constraint foreign key (empleados_id_empleado) references `empleados`(id_empleado) on update cascade on delete cascade
);

create table `ejemplares`
(
codigo_de_barras int not null unique auto_increment,
productos_id_producto int not null,
fecha_preparacion datetime, -- necesario?

-- campos al agregar el producto
entradas_id_entrada int not null,
fecha_adicion datetime not null,

-- campos al vender el producto
ventas_id_venta int,
fecha_venta datetime,
precio_venta decimal(15,2),

-- campos al remover un producto
eliminados_id_eliminado int,
fecha_eliminado datetime,

primary key (codigo_de_barras),
constraint foreign key (productos_id_producto) references `productos`(id_producto) on update cascade on delete cascade,

constraint foreign key (entradas_id_entrada) references `entradas`(id_entrada) on update cascade on delete cascade,
constraint foreign key (ventas_id_venta) references `ventas`(id_venta) on update cascade on delete set null,
constraint foreign key (eliminados_id_eliminado) references `eliminados`(id_eliminado) on update cascade on delete set null
);

create table `pasteles personalizados`
(
id_pastel int not null unique auto_increment,
peso decimal(9,3) not null,
nombre varchar(25) not null, -- nombre del pastel
descripcion varchar(200), -- este podría refinarse más, pero de momento es una sola cadena de caracteres
comentario varchar(75),
costo decimal (15,2) not null,
anticipo decimal (15,2),
fecha_pedido datetime not null,
fecha_entrega datetime not null,
ventas_id_venta int not null unique,

entregado boolean not null,

primary key (id_pastel),
constraint foreign key (ventas_id_venta) references `ventas`(id_venta) on update cascade on delete cascade
);

create table `gestionan`
(
id_ajuste int not null unique auto_increment,
empleados_id_empleado int not null,
pasteles_personalizados_id_pastel int not null,
fecha datetime,

-- copiando toda la tabla para guardar cambios diferenciales
-- todos pueden ser nulos, solo los cambios se almacenan. lo unico que nunca se almacena es la llave primaria o la foránea
c_peso decimal(9,3),
c_nombre varchar(25),
c_descripcion varchar(200),
c_comentario varchar(75),
c_costo decimal (15,2),
c_anticipo decimal (15,2),
c_fecha_pedido datetime, 
c_fecha_entrega datetime,

primary key (id_ajuste),
constraint foreign key (empleados_id_empleado) references `empleados`(id_empleado) on update cascade on delete cascade,
constraint foreign key (pasteles_personalizados_id_pastel) references `pasteles personalizados`(id_pastel) on update cascade on delete cascade
);

-- -----------------
-- PROCEDIMIENTOS
-- -----------------
DELIMITER //

-- ADICIÓN DE PRODUCTOS NUEVOS
drop procedure if exists adicion_de_productos//
create procedure adicion_de_productos
(in _nombre varchar(15), in _descripcion varchar(150), in _precio_unitario decimal(15,2), in _cantidad int, in _fecha_registro datetime, in _id_empleado int)
begin 

declare _id_entrada int;
declare _id_producto int;

-- guarda la entrada
insert into `entradas`(empleados_id_empleado) values(_id_empleado);

set _id_entrada = last_insert_id();

-- guarda el producto
insert into `productos`(nombre, descripcion, precio_unitario, cantidad)
values(_nombre,_descripcion,_precio_unitario, _cantidad);

set _id_producto = last_insert_id();

-- guarda una fila en ejemplar por cada unidad del producto
insercion_ejemplares: loop
	insert into `ejemplares`(productos_id_producto, entradas_id_entrada, fecha_adicion)
    values(_id_producto, _id_entrada, _fecha_registro);
    
	set _cantidad = _cantidad - 1;

	if(_cantidad <= 0) then
		leave insercion_ejemplares;
	end if;
end loop;
end//


-- RESTOCK DE UN PRODUCTO EXISTENTE
drop procedure if exists restock_de_productos//
create procedure restock_de_productos(in _id_producto int, in _cantidad int, in _fecha_registro datetime, in _id_empleado int)
begin

declare _stock_antiguo int;
declare _delta_stock int;
declare _id_entrada int;

-- guarda la entrada
insert into `entradas`(empleados_id_empleado) values(_id_empleado);

set _id_entrada = last_insert_id();

-- obtiene la diferencia con el nuevo número
set _stock_antiguo = (select cantidad from `productos` where id_producto = _id_producto);
set _delta_stock = _cantidad - _stock_antiguo;

-- agrega sólo si la diferencia es positiva
if(_delta_stock > 0) then
	-- realiza el update correspondiente
	update `productos` set cantidad = _cantidad where id_producto = _id_producto;

	-- nuevamente, guarda una fila por ejemplar
	insercion_ejemplares: loop
		insert into `ejemplares`(productos_id_producto, entradas_id_entrada, fecha_adicion)
		values(_id_producto, _id_entrada, _fecha_registro);
		
		set _delta_stock = _delta_stock - 1;

		if(_delta_stock <= 0) then
			leave insercion_ejemplares;
		end if;
	end loop;
end if;
end//


-- VENTA DE UN EJEMPLAR
drop procedure if exists vende_ejemplar//
create procedure vende_ejemplar(in _codigo_barras int, in _id_venta int, in _fecha_venta datetime)
begin 

declare _new_stock int;
declare _id_producto int;
declare _copia_precio decimal(15,2);

-- obtén el producto al cual restarle existencia
set _id_producto = (select productos_id_producto from `ejemplares` where codigo_de_barras = _codigo_barras);
-- obtén el precio del producto en ese momento
set _copia_precio = (select precio_unitario from `productos` where id_producto = _id_producto);
-- resta 1 a la existencia y guardalo en una variable
set _new_stock = (select cantidad from `productos` where id_producto = _id_producto) - 1;

-- actualiza los campos relevantes en el ejemplar
update `ejemplares` set ventas_id_venta = _id_venta, 
fecha_venta = _fecha_venta,
precio_venta = _copia_precio where codigo_de_barras = _codigo_barras;

-- actualiza con la nueva existencia
update `productos` set cantidad = _new_stock where id_producto = _id_producto;
end //


-- AJUSTE DEL MONTO TOTAL DE UNA VENTA
drop procedure if exists ajuste_de_venta//
create procedure ajuste_de_venta(in _id_venta int)
begin 
declare _monto_total decimal(15,2);

set _monto_total = (select sum(precio_venta) from `ventas` inner join `ejemplares` on id_venta = ventas_id_venta);
update `ventas` set monto_total = _monto_total where id_venta = _id_venta;
end //


-- REMOVIENDO UN EJEMPLAR SIN GENERAR UNA VENTA
drop procedure if exists elimina_ejemplar//
create procedure elimina_ejemplar(in _codigo_barras int, in _id_eliminado int, _fecha_eliminado datetime)
begin 
declare _new_stock int;
declare _id_producto int;

set _id_producto = (select productos_id_producto from `ejemplares` where codigo_de_barras = _codigo_barras);
set _new_stock = (select cantidad from `productos` where id_producto = _id_producto) - 1;

-- actualiza los campos relevantes en el ejemplar
update `ejemplares` set eliminados_id_eliminado = _id_eliminado, 
fecha_eliminado = _fecha_eliminado where codigo_de_barras = _codigo_barras;

update `productos` set cantidad = _new_stock where id_producto = _id_producto;
end //


DELIMITER ;
-- -----------------
-- FUNCIONES
-- -----------------
DELIMITER //

drop function if exists nueva_venta//
create function nueva_venta(_id_empleado int) returns int
reads sql data
begin
insert into `ventas`(empleados_id_empleado) values(_id_empleado);
return last_insert_id();
end //

drop function if exists nuevo_eliminado//
create function nuevo_eliminado(_id_empleado int) returns int
reads sql data
begin
insert into `eliminados`(empleados_id_empleado) values(_id_empleado);
return last_insert_id();
end //

DELIMITER ;
-- -----------------
-- TRIGGERS
-- -----------------
DELIMITER //


DELIMITER ;

-- -------------------------
-- USUARIO PARA LA CONEXIÓN
-- -------------------------
drop user if exists 'siv-lf'@'localhost';
create user 'siv-lf'@'localhost' identified by 'EmeraldBadgeGang';

grant all on `siv-lf`.* TO 'siv-lf'@'localhost';

-- -----------------
-- USUARIO DUEÑO
-- -----------------

insert into `empleados`(n_usuario, contra, es_turno_matutino, rol) values('DUEÑO', '$2y$11$Wj/pDFYCXzqhMKv8dy8CUe0X128WAq3Tg/L5sUvYnzZ3xXwCVqZwa', false, 'dueño');


-- 
-- algunos productos
-- 

call adicion_de_productos('uno','rojo','5.00', 4, now(),1);
call adicion_de_productos('dos','naranja','5.50', 20, now(),1);
call adicion_de_productos('tres','amarillo y verde','100.00', 69, now(), 1);
call adicion_de_productos('jhin','verde azulado','0.99', 34, now(),1);
call adicion_de_productos('sinko peso','azul','99.00', 1, now(),1);

