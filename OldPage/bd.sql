-- Crear la base de datos
CREATE DATABASE boutique_db;
GO

USE boutique_db;
GO

-- Tabla de Usuarios
CREATE TABLE Usuarios (
    id_usuario INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
    apellido NVARCHAR(50) NOT NULL,
    correo NVARCHAR(100) UNIQUE NOT NULL,
    contraseña NVARCHAR(255) NOT NULL,
    telefono NVARCHAR(15),
    rol NVARCHAR(20) CHECK (rol IN ('vendedor', 'comprador', 'administrador')) NOT NULL,
    estado NVARCHAR(20) DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
    fecha_registro DATETIME DEFAULT GETDATE()
);
GO

-- Tabla de Categorías
CREATE TABLE Categorias (
    id_categoria INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
    descripcion NVARCHAR(MAX)
);
GO

-- Tabla de Productos
CREATE TABLE Productos (
    id_producto INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(100) NOT NULL,
    descripcion NVARCHAR(MAX),
    modelo NVARCHAR(50),
    talla NVARCHAR(10),
    color NVARCHAR(20),
    precio DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    estado NVARCHAR(20) DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
    id_categoria INT NULL, -- Permitir NULL para evitar problemas con SET NULL
    id_vendedor INT NOT NULL,
    fecha_registro DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id_categoria) ON DELETE SET NULL,
    FOREIGN KEY (id_vendedor) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
);
GO

-- Tabla de Imágenes de Productos
CREATE TABLE ImagenesProductos (
    id_imagen INT IDENTITY(1,1) PRIMARY KEY,
    id_producto INT NOT NULL,
    url_imagen NVARCHAR(MAX) NOT NULL,
    descripcion NVARCHAR(MAX),
    orden INT DEFAULT 1,
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) ON DELETE CASCADE
);
GO

-- Tabla de Carrito de Compras
CREATE TABLE CarritoCompras (
    id_carrito INT IDENTITY(1,1) PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_creacion DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
);
GO

-- Tabla de Detalle del Carrito
CREATE TABLE DetalleCarrito (
    id_detalle INT IDENTITY(1,1) PRIMARY KEY,
    id_carrito INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_carrito) REFERENCES CarritoCompras(id_carrito) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) ON DELETE NO ACTION -- Cambiado a NO ACTION
);
GO

-- Tabla de Ventas
CREATE TABLE Ventas (
    id_venta INT IDENTITY(1,1) PRIMARY KEY,
    id_comprador INT NOT NULL,
    id_vendedor INT NOT NULL,
    fecha_venta DATETIME DEFAULT GETDATE(),
    total DECIMAL(10, 2) NOT NULL,
    estado NVARCHAR(20) DEFAULT 'procesada' CHECK (estado IN ('procesada', 'en transito', 'entregada', 'cancelada')),
    FOREIGN KEY (id_comprador) REFERENCES Usuarios(id_usuario) ON DELETE NO ACTION,
    FOREIGN KEY (id_vendedor) REFERENCES Usuarios(id_usuario) ON DELETE NO ACTION
);
GO

-- Tabla de Detalle de Ventas
CREATE TABLE DetalleVentas (
    id_detalle_venta INT IDENTITY(1,1) PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES Ventas(id_venta) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) ON DELETE CASCADE
);
GO

-- Tabla de Pagos
CREATE TABLE Pagos (
    id_pago INT IDENTITY(1,1) PRIMARY KEY,
    id_venta INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    metodo_pago NVARCHAR(20) CHECK (metodo_pago IN ('tarjeta', 'transferencia', 'efectivo')) NOT NULL,
    estatus NVARCHAR(20) DEFAULT 'pendiente' CHECK (estatus IN ('pagado', 'pendiente', 'rechazado')),
    fecha_pago DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (id_venta) REFERENCES Ventas(id_venta) ON DELETE CASCADE
);
GO

-- Tabla de Direcciones
CREATE TABLE Direcciones (
    id_direccion INT IDENTITY(1,1) PRIMARY KEY,
    id_usuario INT NOT NULL,
    direccion_completa NVARCHAR(MAX) NOT NULL,
    codigo_postal NVARCHAR(10),
    telefono_contacto NVARCHAR(15),
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
);
GO
