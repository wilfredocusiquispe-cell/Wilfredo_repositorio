drop database panaderia;
create database panaderia;
use panaderia;

create table ingrediente (
id_ingrediente int auto_increment primary key,
nombre varchar (100) not null,
unidad_medida varchar (50),
stock decimal(10,2),
precio_unitario decimal(10,2),
    CHECK (stock >= 0),
    CHECK (precio_unitario >= 0)
);

create table pan (
    id_pan int auto_increment primary key,
    nombre varchar(100) NOT NULL,
    descripcion varchar(100),
    precio_venta decimal(10,2) not null,
    stock int not null default 0,

    CHECK (precio_venta >= 0),
    CHECK (stock >= 0)
);

create table detalle_receta (
    id_detalle_receta int auto_increment primary key,
    id_pan int not null,
    id_ingrediente int not null,
    cantidad decimal(10,2) not null,

    CONSTRAINT fk_receta_pan
        FOREIGN KEY (id_pan) REFERENCES pan(id_pan),

    CONSTRAINT fk_receta_ingrediente
        FOREIGN KEY (id_ingrediente) REFERENCES ingrediente(id_ingrediente),

    CHECK (cantidad > 0)
);

create  table produccion (
    id_produccion int auto_increment primary key,
    id_pan int  not null,
    fecha date not null,
    cantidad int not null,
    observaciones varchar(100),

    CONSTRAINT fk_produccion_pan
        FOREIGN KEY (id_pan) REFERENCES pan(id_pan),
    CHECK (cantidad > 0)
);

create table venta (
id_venta int auto_increment primary key,
fecha date not null,
total decimal(10,2),
metodo_pago varchar(100),

    CHECK (total >= 0)
);

create table detalle_venta (
id_detalle_venta int auto_increment primary key,
id_venta int not null,
id_pan int not null,
cantidad int not null,
precio_unitario decimal(10,2) not null,
CHECK (cantidad > 0),
CHECK (precio_unitario >= 0),

    CONSTRAINT fk_detalleventa_venta
	FOREIGN KEY (id_venta) REFERENCES venta(id_venta),
    CONSTRAINT fk_detalleventa_pan
	FOREIGN KEY (id_pan) REFERENCES pan(id_pan)

);

create table proveedor (
    id_proveedor int auto_increment primary key,
    nombre varchar(100) not null,
    telefono varchar(20),
    direccion varchar(100)
);

create table compra (
id_compra int auto_increment primary key,
id_proveedor int not null,
fecha date,
total decimal(10,2),

    CONSTRAINT fk_compra_proveedor
        FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor),
    CHECK (total >= 0)
);

create table detalle_compra (
id_detalle_compra int auto_increment primary key,
id_compra int,
id_ingrediente int,
cantidad decimal(10,2) not null,
precio_unitario decimal(10,2) not null,
CHECK (cantidad > 0),
CHECK (precio_unitario >= 0),

    CONSTRAINT fk_detallecompra_compra
        FOREIGN KEY (id_compra) REFERENCES compra(id_compra),
    CONSTRAINT fk_detallecompra_ingrediente
        FOREIGN KEY (id_ingrediente) REFERENCES ingrediente(id_ingrediente)

);
-- insertar 10 inserciones osea 10 datos para el dia jueves hasta las 4pm

insert into proveedor (nombre, telefono, direccion) values
('Molino Don Bosco', '555-0101', 'Av. Industrial 123'),
('Lácteos y Derivados S.A.', '555-0202', 'Calle Los Pinos 456'),
('Distribuidora de Azúcar El Dulce', '555-0303', 'Zona Comercial B, Lote 12'),
('Levaduras y Fermentos del Sur', '555-0404', 'Av. Las Palmas 789'),
('Huevos y Avícola San Juan', '555-0505', 'Carretera Norte Km 4'),
('Especias y Semillas del Valle', '555-0606', 'Mercado Central Puesto 45'),
('Margarinas y Grasas Industriales', '555-0707', 'Zona Franca Nave 3'),
('Frutos Secos y Semillas S.R.L.', '555-0808', 'Calle Comercio 321'),
('Empaques y Envases Panaderos', '555-0909', 'Av. Los Próceres 654'),
('Suministros Generales de Panadería', '555-1010', 'Parque Industrial Sur');

insert into ingrediente (nombre, unidad_medida, stock, precio_unitario) values
('Harina de trigo', 'Kg', 500.00, 1.20),
('Agua', 'Litros', 1000.00, 0.00),
('Levadura fresca', 'Kg', 25.00, 4.50),
('Sal fina', 'Kg', 50.00, 0.80),
('Azúcar blanca', 'Kg', 100.00, 1.50),
('Mantequilla sin sal', 'Kg', 40.00, 5.00),
('Huevos', 'Unidades', 300.00, 0.15),
('Leche entera', 'Litros', 80.00, 1.10),
('Esencia de vainilla', 'Litros', 5.00, 15.00),
('Ajonjolí (Sésamo)', 'Kg', 10.00, 6.50);

insert into venta (fecha, total, metodo_pago) values
('2026-01-09', 15.40, 'Efectivo'),
('2026-04-08', 28.50, 'Tarjeta'),
('2026-03-10', 12.00, 'Efectivo'),
('2026-05-10', 45.20, 'Efectivo'),
('2026-10-11', 8.50, 'QR / Transferencia'),
('2026-12-11', 34.00, 'Tarjeta'),
('2026-09-12', 19.80, 'Efectivo'),
('2026-06-12', 52.00, 'Tarjeta'),
('2026-11-15', 22.50, 'Efectivo'), 
('2026-10-23', 38.90, 'Tarjeta');  

insert into pan (nombre, descripcion, precio_venta, stock) values
('Pan Francés', 'Pan crujiente tradicional', 0.20, 150),
('Pan Integral', 'Pan de molde con fibra', 0.50, 40),
('Pan de Manteca', 'Pan suave y dulce', 0.30, 80),
('Baguette', 'Pan alargado corteza dura', 1.00, 25),
('Pan de Yema', 'Enriquecido con yema', 0.35, 60),
('Croissant', 'Hojaldre de mantequilla', 1.50, 30),
('Pan de Centeno', 'Pan oscuro y denso', 0.80, 20),
('Cachito Jamón/Queso', 'Pan relleno salado', 2.00, 15),
('Pan Hamburguesa', 'Paquete x4 unidades', 1.80, 35),
('Pan Dulce / Concha', 'Con costra de azúcar', 0.60, 50);

insert into detalle_venta (id_venta, id_pan, cantidad, precio_unitario) values
(1, 1, 10, 0.20),
(1, 3, 4, 0.30),
(2, 6, 5, 1.50),
(2, 2, 4, 0.50),
(3, 1, 15, 0.20),
(4, 8, 5, 2.00),
(4, 9, 3, 1.80),
(5, 5, 10, 0.35),
(6, 4, 10, 1.00),
(7, 10, 15, 0.60);

insert into detalle_receta (id_pan, id_ingrediente, cantidad) values
(1, 1, 0.10), -- Pan Francés requiere Harina
(1, 4, 0.01), -- Pan Francés requiere Sal
(2, 1, 0.08), -- Pan Integral requiere Harina
(2, 5, 0.01), -- Pan Integral requiere Azúcar
(3, 1, 0.12), -- Pan de Manteca requiere Harina
(3, 6, 0.02), -- Pan de Manteca requiere Mantequilla
(5, 7, 1.00), -- Pan de Yema requiere Huevo
(5, 3, 0.01), -- Pan de Yema requiere Levadura
(6, 6, 0.05), -- Croissant requiere Mantequilla
(6, 1, 0.15); -- Croissant requiere Harina

insert into compra (id_proveedor, fecha, total) values
(1, '2026-11-09', 600.00),
(4, '2026-11-09', 112.50),
(3, '2026-11-10', 150.00),
(5, '2026-11-10', 45.00),
(2, '2026-11-11', 88.00),
(7, '2026-11-11', 200.00),
(6, '2026-11-12', 32.50),
(8, '2026-11-12', 65.00),
(1, '2026-11-13', 300.00), 
(4, '2026-11-13', 45.00);  

insert into detalle_compra (id_compra, id_ingrediente, cantidad, precio_unitario) values
(1, 1, 500.00, 1.20),
(2, 3, 25.00, 4.50),
(3, 5, 100.00, 1.50),
(4, 7, 300.00, 0.15),
(5, 8, 80.00, 1.10),
(6, 6, 40.00, 5.00),
(7, 10, 5.00, 6.50),
(8, 9, 4.00, 15.00),
(9, 1, 250.00, 1.20),
(10, 3, 10.00, 4.50);

insert into produccion (id_pan, fecha, cantidad, observaciones) values
(1, '2026-11-09', 200, 'Primer lote de la semana'),
(2, '2026-11-09', 50, 'Horneado matutino'),
(3, '2026-11-10', 100, 'Buena consistencia'),
(4, '2026-11-10', 30, 'Corto stock de baguettes'),
(5, '2026-11-11', 80, 'Lote estándar'),
(6, '2026-11-11', 40, 'Croissants dorados al punto'),
(7, '2026-11-12', 25, 'Pan de centeno especial'),
(8, '2026-11-12', 20, 'Cachitos salados'),
(1, '2026-11-13', 150, 'Producción jueves mañana'),
(3, '2026-11-13', 60, 'Producción jueves antes de las 4pm');

-- INDEX
create index idx_pan_nombre on pan(nombre);
create index idx_venta_fecha on venta(fecha);

-- DISPARADORES
drop trigger if exists trg_validar_stock_pan;
delimiter $$
create trigger trg_validar_stock_pan
before update on pan
for each row
begin
if new.stock < 0 then
signal sqlstate '45000'
set message_text = 'Error de integridad: El stock de un pan no puede ser negativo';
end if;
end $$
delimiter ;

-- Registro en bitacora todo cambio en el stock
delimiter $$
drop trigger if exists trg_auditoria_pan$$
create trigger trg_auditoria_pan
after update on pan
for each row
begin
    if old.stock <> new.stock then
        insert into bitacora_sistema(evento, descripcion, usuario_db)
        values('STOCK_MODIFICADO',
        concat('Pan: ', new.nombre, ' | Stock anterior ', old.stock, ' | Stock nuevo: ', new.stock),
        current_user());
    end if;
end $$
delimiter ;

-- transaccion segura
delimiter $$
drop procedure if exists sp_registrar_venta_segura$$
create procedure sp_registrar_venta_segura (
    in p_id_pan int,
    in p_cantidad int,
    in p_metodo_pago varchar(100)
)
begin
    -- 1. Declaración de variables (siempre al inicio)
    declare v_stock int default 0;
    declare v_precio decimal(10,2) default 0.00;
    declare v_id_venta int default 0;
    -- 2. Declaración del manejador de errores estructurado correctamente
    declare exit handler for sqlexception
    begin
        rollback;
        select 'Error critico: Transaccion revertida por fallo' as resultado;
    end;
    -- 3. Lógica ejecutable de la transacción
    start transaction;
    -- Disponibilidad de stock y precio del pan
    select stock, precio_venta into v_stock, v_precio
    from pan
    where id_pan = p_id_pan
    for update;
    if p_cantidad <= 0 or v_stock < p_cantidad then
        signal sqlstate '45000'
        set message_text = 'Stock insuficiente o cantidad invalida para registrar la venta';
    end if;
    -- Descuento de unidades en inventario
    update pan
    set stock = stock - p_cantidad
    where id_pan = p_id_pan;
    -- Savepoint tras descuento de stock
    savepoint stock_descontado;
    -- Registro de la venta
    insert into venta(fecha, total, metodo_pago)
    values (curdate(), v_precio * p_cantidad, p_metodo_pago);
    set v_id_venta = last_insert_id();
    -- Registro del detalle de la venta
    insert into detalle_venta(id_venta, id_pan, cantidad, precio_unitario)
    values (v_id_venta, p_id_pan, p_cantidad, v_precio);
    commit;
    select 'Exito: Venta registrada y stock actualizado.' as resultado;
end$$
delimiter ;

-- cursor para calcular el valor total del inventario (panes + ingredientes)
delimiter $$
drop procedure if exists sp_valorizar_inventario$$
create procedure sp_valorizar_inventario()
begin
    -- 1. Declaración de variables y cursores al inicio
    declare v_stock decimal(12,2) default 0.00;
    declare v_costo decimal(10,2) default 0.00;
    declare v_valor_total decimal(12,2) default 0.00;
    declare v_fin int default 0;
    -- Declaración del cursor
    declare cur_inventario cursor for
        select stock, precio_venta from pan
        union all
        select stock, precio_unitario from ingrediente;
    -- Manejador para el fin del cursor (NOT FOUND)
    declare continue handler for not found set v_fin = 1;
    -- 2. Lógica del procedimiento
    open cur_inventario;
    bucle_valorizacion: loop
        fetch cur_inventario into v_stock, v_costo;
        if v_fin = 1 then
            leave bucle_valorizacion;
        end if;
        set v_valor_total = v_valor_total + (v_stock * v_costo);
    end loop bucle_valorizacion;
    close cur_inventario;
    -- Resultado final
    select v_valor_total as valor_total_inventario_usd;
end$$
delimiter ;


-- Vista administrativa del inventario de panes
create or replace view vw_inventario_panes as
select p.id_pan, p.nombre, p.descripcion, p.precio_venta, p.stock,
    case
        when p.stock = 0 then 'SIN STOCK'
        when p.stock <= 10 then 'STOCK BAJO'
        else 'STOCK DISPONIBLE'
    end as estado_stock
from pan p;

-- vista para consultar el detalle de las ventas.
create or replace view vw_ventas_detalle as
select v.id_venta, v.fecha, v.metodo_pago, p.nombre as pan,
dv.cantidad, dv.precio_unitario, (dv.cantidad * dv.precio_unitario) as subtotal, v.total
from venta v
inner join detalle_venta dv on v.id_venta = dv.id_venta
inner join pan p on dv.id_pan = p.id_pan;

-- rol operativo
create role if not exists 'rol_asistente_panaderia';

-- Permisos del asistente de panadería
grant select on panaderia.vw_inventario_panes TO 'rol_asistente_panaderia';
grant select on panaderia.vw_ventas_detalle TO 'rol_asistente_panaderia';


create user if not exists 'asistente_web'@'localhost' identified by 'PanSecure2026';
-- asignacion de rol usuario
grant 'rol_asistente_panaderia' to 'asistente_web'@'localhost';
set default role 'rol_asistente_panaderia' to 'asistente_web'@'localhost';
flush privileges;

-- pruebas de funcionamiento
call sp_valorizar_inventario();
call sp_registrar_venta_segura(1, 2, 'Efectivo');
select * from bitacora_sistema order by id_bitacora desc;
select * from vw_ventas_detalle;

