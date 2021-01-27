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
rol enum('dueño','empleados') not null default 'empleados',
es_turno_matutino boolean not null,

-- variables de config
filas_max_inventario int not null default 15,
pag_max_inventario int not null default 15,

filas_max_productos int not null default 6,
pag_max_productos int not null default 15,

-- para iniciar sesión
n_usuario char(20) not null unique,
contra char(255) not null unique, -- se guarda el valor codificado, no la contraseña per se

primary key (id_empleado)
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
(in _nombre varchar(50), in _descripcion varchar(150), in _precio_unitario decimal(15,2), in _cantidad int, in _fecha_registro datetime, in _id_empleado int)
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


-- a
drop function if exists nueva_venta//
create function nueva_venta(_id_empleado int) returns int
reads sql data
begin
insert into `ventas`(empleados_id_empleado) values(_id_empleado);
return last_insert_id();
end //


-- a
drop function if exists nuevo_eliminado//
create function nuevo_eliminado(_id_empleado int) returns int
reads sql data
begin
insert into `eliminados`(empleados_id_empleado) values(_id_empleado);
return last_insert_id();
end //

-- CONTANDO LOS PRODUCTOS DIFERENTES EN EL INVENTARIO
drop function if exists cuenta_inventario//
create function cuenta_inventario() returns int
reads sql data
begin

return (select count(id_producto) from `productos`);

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

call adicion_de_productos('Patchouli Knowledge','In der Dunkelheit, ganz umhüllt von tiefer Schwärze', 100.14, 88, now(),1);
call adicion_de_productos('Patchouli Knowledge','Tanzt die Apathie doch ich werde ihrem Schritt nicht folgen', 112.00, 26, now(),1);
call adicion_de_productos('Remilia Scarlet','Mein Blick ist verhüllt und Ich kann mein Herz nicht sehen', 166.69, 58, now(), 1);
call adicion_de_productos('Remilia Scarlet','Selbst wenn es zerbricht interessiert - es- mich nicht',82.93, 19, now(),1);

call adicion_de_productos('Sakuya Izayoi','Ich beweg mich nicht, stehe still und warte schweigend', 242.57, 92, now(),1);
call adicion_de_productos('Sakuya Izayoi','Doch es trägt mich fort denn die Risse der Zeit greifen nach mir', 236.76, 33, now(),1);
call adicion_de_productos('Flandre Scarlet','Es berührt mich nicht, ich will all das, nur vergessen', 173.07, 66, now(), 1);
call adicion_de_productos('Flandre Scarlet','Ich bin wie ich bin und mehr zählt ja auch nicht.',173.53, 54, now(),1);

call adicion_de_productos('Youmu Konpaku','Träum ich einen Traum? Sehe ich die Wirklichkeit?', 237.28, 71, now(),1);
call adicion_de_productos('Youmu Konpaku','Meine Worte helfen nicht, denn ich bin noch nicht bereit.', 176.95, 77, now(),1);
call adicion_de_productos('Yuyuko Saigyouji','Und die Traurigkeit in mir, erschöpft mich in dieser Zeit', 79.97, 81, now(), 1);
call adicion_de_productos('Yuyuko Saigyouji','Lieber würde ich nichts fühl\'n, wäre von dem Leid befreit.', 30.93, 27, now(),1);

call adicion_de_productos('Komachi Onozuka','Was du sagst versteh ich nicht, es verwirrt mich fürchterlich', 123.06, 33, now(),1);
call adicion_de_productos('Komachi Onozuka','Mein Herz halte ich versteckt und Gefühle unentdeckt', 124.16, 71, now(),1);
call adicion_de_productos('Eiki Shikieiki','Jag ich meinen Träumen nach werde ich bloß wieder schwach', 190.25, 78, now(), 1);
call adicion_de_productos('Eiki Shikieiki','Und so ich reiße alles mit in die tiefe Dunkelheit', 150.75, 67, now(),1);

call adicion_de_productos('Fujiwara no Mokou','Hält denn jemand so wie ich seine Zukunft in der Hand', 191.03, 86, now(),1);
call adicion_de_productos('Fujiwara no Mokou','Gehör\' ich in diese Welt oder hab ich\'s nicht verdient', 231.52, 99, now(),1);
call adicion_de_productos('Keine Kamishirasawa Normal','Warum schmerzt mein Herz so sehr? um wen trauer ich denn nur?', 129.71, 50, now(), 1);
call adicion_de_productos('Keine Kamishirasawa Hakutaku','Ich versteh mich selber nicht seh im Spiegel kein Gesicht.', 230.82, 78, now(),1);

call adicion_de_productos('Eirin Yagokoro','Und ich laufe ohne Ziel, ist das alles nur ein Spiel', 104.00, 75, now(),1);
call adicion_de_productos('Eirin Yagokoro','Werd geblendet von dem Licht doch erreichen kann ich\'s nicht.', 148.76, 92, now(),1);
call adicion_de_productos('Kaguya Houraisan','Könnte ich \'ne Andere sein bleib ich sicher nicht allein', 174.93, 45, now(), 1);
call adicion_de_productos('Kaguya Houraisan','Und dreh ich mich zu dem Schein hüllt er mich letztendlich ein', 80.81, 45, now(),1);

call adicion_de_productos('Sanae Kochiya','Und die Zeit vergeht, fließt vorbei und in dem Lichte', 80.43, 26, now(),1);
call adicion_de_productos('Hina Kagiyama','Tanzt die Apathie und ich werde ihrem Schritt nun folgen', 142.16, 57, now(),1);
call adicion_de_productos('Kanako Yasaka','Mein Blick ist verhüllt und ich kann mein Herz nicht sehen', 78.48, 28, now(), 1);
call adicion_de_productos('Suwako Moriya','Selbst wenn es zerbricht interessiert - es - mich nicht', 50.41, 31, now(),1);

call adicion_de_productos('Yukari Yakumo','Ich beweg mich nicht, stehe still und warte schweigend', 79.52, 17, now(),1);
call adicion_de_productos('Tenshi Hinanawi','Doch es trägt mich fort denn die Risse der Zeit greifen nach mir', 112.52, 51, now(),1);
call adicion_de_productos('Yukari Yakumo','Es berührt mich nicht, ich will all das, nur vergessen', 13.67, 11, now(), 1);
call adicion_de_productos('Tenshi Hinanawi','Ich bin wie ich bin und mehr zählt ja auch nicht.',137.31, 11, now(),1);

call adicion_de_productos('Aya Shameimaru','Träum ich einen Traum? Sehe ich die Wirklichkeit?', 211.49, 27, now(),1);
call adicion_de_productos('Aya Shameimaru','Meine Worte helfen nicht, denn ich bin noch nicht bereit.', 153.20, 22, now(),1);
call adicion_de_productos('Suika Ibuki','Und die Traurigkeit in mir, erschöpft mich in dieser Zeit', 25.41, 25, now(), 1);
call adicion_de_productos('Suika Ibuki','Lieber würde ich nichts fühl\'n, wäre von dem Leid befreit.', 70.17, 95, now(),1);

call adicion_de_productos('Alice Margatroid','Was du sagst versteh ich nicht, es verwirrt mich fürchterlich', 244.79, 97, now(),1);
call adicion_de_productos('Alice Margatroid','Mein Herz halte ich versteckt und Gefühle unentdeckt', 116.02, 42, now(),1);
call adicion_de_productos('Nitori Kawashiro','Jag ich meinen Träumen nach werde ich bloß wieder schwach', 87.40, 28, now(), 1);
call adicion_de_productos('Nitori Kawashiro','Und so ich reiße alles mit in die tiefe Dunkelheit', 31.12, 30, now(),1);

call adicion_de_productos('Yuuka Kazami','Mache ich nur einen Schritt. Mache ich nur einen Schritt',212.41, 37, now(),1);
call adicion_de_productos('Yuuka Kazami','Zerstör ich das was ich lieb. Zerstör ich das was ich lieb', 157.62, 46, now(),1);
call adicion_de_productos('Elly','Drück ich meine Trauer aus. Drück ich meine Trauer aus', 19.15, 91, now(), 1);
call adicion_de_productos('Elly','Wird mein Herz dann wieder rein. Taucht in weisse Farbe ein', 215.35, 92, now(),1);

call adicion_de_productos('Yuuka Kazami','Ich weiß gar nichts über dich. Ich weiß gar nichts über mich',133.37, 65, now(),1);
call adicion_de_productos('Yuuka Kazami','Ich versteh mich selber nicht. Meine Welt hat kein Gesicht', 80.76, 49, now(),1);
call adicion_de_productos('Reimu Hakurei','Mach ich meine Augen auf. Nimmt das Unheil seinen Lauf', 11.53, 88, now(), 1);
call adicion_de_productos('Marisa Kirisame','Und nun zerr ich alles mit in die tiefe Dunkelheit', 78.16, 58, now(),1);