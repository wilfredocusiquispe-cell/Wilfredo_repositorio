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

create table pan(
id_pan int auto_increment primary key,
nombre varchar (100) not null,
descripcion varchar (50),
precio_venta decimal(10,2) not null,
    CHECK (precio_venta >= 0)
);

create table detalle_receta (
id_detalle_receta int auto_increment primary key,
id_pan int,
id_ingrediente int,
cantidad decimal(10,2),

    CONSTRAINT fk_receta_pan
	FOREIGN KEY (id_pan) REFERENCES pan(id_pan),
    CONSTRAINT fk_receta_ingrediente
	FOREIGN KEY (id_ingrediente) REFERENCES ingrediente(id_ingrediente)
);

create table produccion (
id_produccion int auto_increment primary key,
id_pan int,
fecha date,
cantidad int,
observaciones varchar(100),

    CONSTRAINT fk_produccion_pan
	FOREIGN KEY (id_pan) REFERENCES pan(id_pan)
    
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
telefono int,
direccion varchar(50)
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