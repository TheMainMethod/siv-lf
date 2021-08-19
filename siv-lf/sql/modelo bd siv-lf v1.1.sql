drop database if exists `siv-lf`;
create database `siv-lf`;

use `siv-lf`;

create table `productos`
(
id_producto int not null unique auto_increment, -- later
nombre varchar(50) not null,
descripcion varchar(150),
precio_unitario decimal(15,2) not null,
cantidad int not null,
fecha_registro datetime not null,
fecha_preparacion datetime,

primary key (id_producto)
);

create table `ejemplares`
(
codigo_de_barras int not null unique auto_increment,
productos_id_producto int not null,

primary key (codigo_de_barras),
constraint foreign key (productos_id_producto) references `productos`(id_producto) on update cascade on delete cascade
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

create table `ventas`
(
id_venta int not null unique auto_increment,
monto_total decimal(15,2) not null, -- sale casi sobrando, pero también podría consultarse para evitar realizar muchas operaciones
empleados_id_empleado int not null unique,

primary key (id_venta),
constraint foreign key (empleados_id_empleado) references `empleados`(id_empleado) on update cascade on delete cascade
);

create table `aumentan`
(
ventas_id_venta int not null unique,
productos_id_producto int not null unique,
fecha datetime not null,
cantidad int not null,

primary key (ventas_id_venta, productos_id_producto),
constraint foreign key (ventas_id_venta) references `ventas`(id_venta) on update cascade on delete cascade,
constraint foreign key (productos_id_producto) references `productos`(id_producto) on update cascade on delete cascade
);

create table `reducen`
(
ventas_id_venta int not null unique,
productos_id_producto int not null unique,
fecha datetime not null,
cantidad int not null,
precio_unitario int not null, -- copiado de la tabla productos. no referenciado en caso de que el precio per se llegara a cambiar en el futuro,
-- causando alguna discrepancia en los reportes.

primary key (ventas_id_venta, productos_id_producto),
constraint foreign key (ventas_id_venta) references `ventas`(id_venta) on update cascade on delete cascade,
constraint foreign key (productos_id_producto) references `productos`(id_producto) on update cascade on delete cascade
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

drop procedure if exists agrega_ejemplares//
create procedure agrega_ejemplares(in _id_producto int, in _cantidad int)
begin 
	-- update `productos` set cantidad = cantidad + _cantidad where id_producto = _id_producto;
insercion_ejemplares: loop
	insert into `ejemplares`(productos_id_producto) values(_id_producto);
	set _cantidad = _cantidad - 1;

	if(_cantidad <= 0) then
		leave insercion_ejemplares;
	end if;
end loop;

end //

drop procedure if exists remueve_ejemplares//
create procedure remueve_ejemplares(in _id_producto int)
begin 

declare new_stock int;

set new_stock = (select cantidad from `productos` where id_producto = _id_producto) - 1;

update `productos` set cantidad = new_stock where id_producto = _id_producto;

end //


DELIMITER ;
-- -----------------
-- FUNCIONES
-- -----------------



-- -----------------
-- TRIGGERS
-- -----------------
DELIMITER //

drop trigger if exists productos_ai//
create trigger productos_ai after insert on `productos` for each row
begin

	call agrega_ejemplares(new.id_producto,new.cantidad);

end //

-- falta
drop trigger if exists productos_au//
create trigger productos_au after update on `productos` for each row
begin
	declare delta_cantidad int;

	if(new.cantidad > old.cantidad) then
		set delta_cantidad = new.cantidad - old.cantidad;
    
		call agrega_ejemplares(new.id_producto,delta_cantidad);
    end if;

end //


drop trigger if exists ejemplares_bd//
create trigger ejemplares_bd before delete on `ejemplares` for each row
begin

	call remueve_ejemplares(old.productos_id_producto);

end //


DELIMITER ;
-- -----------------
-- USUARIO DUEÑO
-- -----------------

insert into `empleados`(n_usuario, contra, es_turno_matutino, rol) values('DUEÑO', '$2y$11$Wj/pDFYCXzqhMKv8dy8CUe0X128WAq3Tg/L5sUvYnzZ3xXwCVqZwa', false, 'dueño');


-- 
-- algunos productos
-- 

insert into `productos`(nombre, descripcion, precio_unitario, cantidad, fecha_registro) values('uno','rojo','5.00', 4, now());
insert into `productos`(nombre, descripcion, precio_unitario, cantidad, fecha_registro) values('dos','naranja','5.50', 20, now());
insert into `productos`(nombre, descripcion, precio_unitario, cantidad, fecha_registro) values('tres','amarillo y verde','100.00', 69, now());
insert into `productos`(nombre, descripcion, precio_unitario, cantidad, fecha_registro) values('jhin','verde azulado','0.99', 34, now());
insert into `productos`(nombre, descripcion, precio_unitario, cantidad, fecha_registro) values('sinko peso','azul','99.00', 1, now());

