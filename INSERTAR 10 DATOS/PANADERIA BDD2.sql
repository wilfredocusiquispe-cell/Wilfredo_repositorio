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
cantidad int,
precio_unitario decimal(10,2),

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
cantidad decimal(10,2),
precio_unitario decimal(10,2),

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

INSERT INTO detalle_venta (id_venta, id_pan, cantidad, precio_unitario) VALUES
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