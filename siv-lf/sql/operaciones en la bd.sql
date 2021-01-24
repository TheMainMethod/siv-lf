select * from `productos`;

select * from `ejemplares`;

select * from `entradas`;

select * from `ventas`;

-- cuenta todos, incluso vendidos y eliminados
select productos_id_producto, count(productos_id_producto) 
from `ejemplares` group by productos_id_producto;

-- cuenta los que no han sido vendidos ni eliminados
select productos_id_producto, count(productos_id_producto) 
from `ejemplares` where ventas_id_venta is null and eliminados_id_eliminado is null
group by productos_id_producto;

-- (mejor manejado desde la aplicación, por las contraseñas)
-- insertando nuevo empleado LISTO
-- cambiando el rol de un empleado FALTA
-- restaurando la contraseña de un empleado FALTA


-- agregando un producto completamente nuevo
-- nombre, descripcion, precio unitario, cantidad, fecha registro, id_empleado
call adicion_de_productos('ben 10','jaj','999.00', 4, now(), 1);


-- añadiendo stock a un producto existente
-- id_producto, nueva cantidad (NO cantidad a agregar), fecha registro, empleado
call restock_de_productos(3, 70, now(), 1);
call restock_de_productos(1, 10, now(), 1);


-- vendiendo un producto
-- id_empleado
set @nueva_venta = nueva_venta(1);
-- codigo de barras, id_venta, fecha venta, precio venta (Del ejemplar)
call vende_ejemplar(10, @nueva_venta, now()); -- 5.50
call vende_ejemplar(11, @nueva_venta, now()); -- 5.50
call vende_ejemplar(30, @nueva_venta, now()); -- 100.00, total 111.00
-- id_venta
call ajuste_de_venta(@nueva_venta);
set @nueva_venta = null;


-- descartando un producto no vendido por razones varias
-- id_empleado
set @nuevo_eliminado = nuevo_eliminado(1);
-- codigo de barras, id_eliminado, fecha eliminado
call elimina_ejemplar(12, @nuevo_eliminado, now());
set @nuevo_eliminado = null;


-- buscando un producto

-- devolviendo 10 mas vendidos


-- ordenando un pastel

-- actualizando el pastel

-- cancelando el pastel

-- vendiendo el pastel


-- consultando las entradas en un mes

-- consultando las ventas en un mes

-- consultando los eliminados en un mes