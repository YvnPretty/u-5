-- SQL Implementation for Inventory Management System
-- Target RDBMS: MySQL

-- -----------------------------------------------------
-- Table `Usuarios`
-- Stores user credentials and roles
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Usuarios` (
  `ID_Usuario` INT NOT NULL AUTO_INCREMENT,
  `NombreUsuario` VARCHAR(50) NOT NULL UNIQUE,
  `Contrasena` VARCHAR(255) NOT NULL, -- Store hashed passwords
  `Rol` VARCHAR(20) NOT NULL COMMENT 'Puede ser ''root'' o ''usuario''',
  `NombreCompleto` VARCHAR(100) NULL,
  `FechaCreacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Usuario`))
ENGINE = InnoDB;

-- Insert sample users
INSERT INTO `Usuarios` (`NombreUsuario`, `Contrasena`, `Rol`, `NombreCompleto`) VALUES
('root', 'hashed_password_root', 'root', 'Administrador Principal'), -- Replace with actual hashed password
('user1', 'hashed_password_user1', 'usuario', 'Empleado Uno');    -- Replace with actual hashed password

-- -----------------------------------------------------
-- Table `Categorias`
-- Stores product categories
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Categorias` (
  `ID_Categoria` INT NOT NULL AUTO_INCREMENT,
  `NombreCategoria` VARCHAR(100) NOT NULL UNIQUE,
  `DescripcionCategoria` TEXT NULL,
  PRIMARY KEY (`ID_Categoria`))
ENGINE = InnoDB;

-- Insert sample categories
INSERT INTO `Categorias` (`NombreCategoria`, `DescripcionCategoria`) VALUES
('Electrónicos', 'Dispositivos y componentes electrónicos'),
('Libros', 'Libros de diversas temáticas'),
('Alimentos', 'Productos alimenticios no perecederos'),
('Herramientas', 'Herramientas manuales y eléctricas');

-- -----------------------------------------------------
-- Table `Proveedores`
-- Stores supplier information (optional but recommended)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Proveedores` (
  `ID_Proveedor` INT NOT NULL AUTO_INCREMENT,
  `NombreProveedor` VARCHAR(150) NOT NULL,
  `Contacto` VARCHAR(100) NULL,
  `Telefono` VARCHAR(20) NULL,
  `Email` VARCHAR(100) NULL,
  PRIMARY KEY (`ID_Proveedor`))
ENGINE = InnoDB;

-- Insert sample suppliers
INSERT INTO `Proveedores` (`NombreProveedor`, `Contacto`, `Telefono`, `Email`) VALUES
('TechDist S.A.', 'Juan Perez', '555-1234', 'ventas@techdist.com'),
('Ediciones Global', 'Ana Lopez', '555-5678', 'pedidos@edglobal.com'),
('Almacenes ABC', 'Carlos Ruiz', '555-8765', 'contacto@almacenesabc.com');

-- -----------------------------------------------------
-- Table `Productos`
-- Stores product details
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Productos` (
  `ID_Producto` INT NOT NULL AUTO_INCREMENT,
  `CodigoProducto` VARCHAR(50) NOT NULL UNIQUE,
  `NombreProducto` VARCHAR(150) NOT NULL,
  `DescripcionProducto` TEXT NULL,
  `ID_Categoria` INT NOT NULL,
  `ID_Proveedor` INT NULL,
  `PrecioCompra` DECIMAL(10,2) NULL COMMENT 'Solo visible/editable por el Root',
  `PrecioVenta` DECIMAL(10,2) NOT NULL,
  `StockActual` INT NOT NULL DEFAULT 0,
  `StockMinimo` INT NOT NULL DEFAULT 5,
  `UnidadMedida` VARCHAR(20) NULL DEFAULT 'unidad',
  `FechaAgregado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `UltimaActualizacionStock` TIMESTAMP NULL,
  PRIMARY KEY (`ID_Producto`),
  INDEX `fk_Productos_Categorias_idx` (`ID_Categoria` ASC),
  INDEX `fk_Productos_Proveedores_idx` (`ID_Proveedor` ASC),
  CONSTRAINT `fk_Productos_Categorias`
    FOREIGN KEY (`ID_Categoria`)
    REFERENCES `Categorias` (`ID_Categoria`)
    ON DELETE RESTRICT -- Prevent deleting category if products are associated
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Productos_Proveedores`
    FOREIGN KEY (`ID_Proveedor`)
    REFERENCES `Proveedores` (`ID_Proveedor`)
    ON DELETE SET NULL -- If supplier is deleted, set to NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- Insert sample products (at least 10 for user view)
INSERT INTO `Productos` (`CodigoProducto`, `NombreProducto`, `ID_Categoria`, `ID_Proveedor`, `PrecioCompra`, `PrecioVenta`, `StockActual`, `StockMinimo`, `UnidadMedida`) VALUES
('ELEC001', 'Laptop Gamer X', 1, 1, 800.00, 1200.00, 15, 5, 'unidad'),
('ELEC002', 'Mouse Óptico', 1, 1, 10.00, 25.00, 50, 10, 'unidad'),
('LIB001', 'El Quijote', 2, 2, 5.00, 15.00, 30, 5, 'unidad'),
('LIB002', 'Cien Años de Soledad', 2, 2, 7.00, 18.00, 25, 5, 'unidad'),
('ALIM001', 'Arroz Bolsa 1kg', 3, 3, 0.80, 1.50, 100, 20, 'kg'),
('ALIM002', 'Frijol Bolsa 1kg', 3, 3, 1.00, 2.00, 80, 20, 'kg'),
('HERR001', 'Martillo Carpintero', 4, NULL, 8.00, 15.00, 40, 10, 'unidad'),
('HERR002', 'Destornillador Estrella', 4, NULL, 3.00, 7.00, 60, 15, 'unidad'),
('ELEC003', 'Teclado Mecánico RGB', 1, 1, 45.00, 75.00, 22, 5, 'unidad'),
('LIB003', 'Física Universitaria Vol. 1', 2, 2, 25.00, 40.00, 18, 3, 'unidad');


-- -----------------------------------------------------
-- Table `MovimientosInventario`
-- Logs all stock movements
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `MovimientosInventario` (
  `ID_Movimiento` INT NOT NULL AUTO_INCREMENT,
  `ID_Producto` INT NOT NULL,
  `TipoMovimiento` VARCHAR(50) NOT NULL COMMENT 'Ej: ''ENTRADA_COMPRA'', ''SALIDA_VENTA'', ''AJUSTE_POSITIVO'', ''AJUSTE_NEGATIVO'', ''ENTRADA_INICIAL''',
  `Cantidad` INT NOT NULL COMMENT 'Siempre positivo, el TipoMovimiento define la operación',
  `FechaMovimiento` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_Usuario` INT NOT NULL,
  `PrecioUnitarioMovimiento` DECIMAL(10,2) NULL COMMENT 'Precio al momento del movimiento',
  `Notas` TEXT NULL,
  PRIMARY KEY (`ID_Movimiento`),
  INDEX `fk_MovimientosInventario_Productos_idx` (`ID_Producto` ASC),
  INDEX `fk_MovimientosInventario_Usuarios_idx` (`ID_Usuario` ASC),
  CONSTRAINT `fk_MovimientosInventario_Productos`
    FOREIGN KEY (`ID_Producto`)
    REFERENCES `Productos` (`ID_Producto`)
    ON DELETE RESTRICT -- Prevent deleting product if movements exist
    ON UPDATE CASCADE,
  CONSTRAINT `fk_MovimientosInventario_Usuarios`
    FOREIGN KEY (`ID_Usuario`)
    REFERENCES `Usuarios` (`ID_Usuario`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `LogPrecios`
-- Logs changes to product prices for auditing
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LogPrecios` (
  `ID_Log` INT NOT NULL AUTO_INCREMENT,
  `ID_Producto` INT NOT NULL,
  `PrecioCompraAnterior` DECIMAL(10,2) NULL,
  `PrecioCompraNuevo` DECIMAL(10,2) NULL,
  `PrecioVentaAnterior` DECIMAL(10,2) NULL,
  `PrecioVentaNuevo` DECIMAL(10,2) NULL,
  `ID_UsuarioModifico` INT NOT NULL,
  `FechaModificacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Log`),
  INDEX `fk_LogPrecios_Productos_idx` (`ID_Producto` ASC),
  INDEX `fk_LogPrecios_Usuarios_idx` (`ID_UsuarioModifico` ASC),
  CONSTRAINT `fk_LogPrecios_Productos`
    FOREIGN KEY (`ID_Producto`)
    REFERENCES `Productos` (`ID_Producto`)
    ON DELETE CASCADE, -- If product is deleted, log can be deleted or kept based on policy
  CONSTRAINT `fk_LogPrecios_Usuarios`
    FOREIGN KEY (`ID_UsuarioModifico`)
    REFERENCES `Usuarios` (`ID_Usuario`)
    ON DELETE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Triggers
-- -----------------------------------------------------
DELIMITER $$

-- Trigger: TRG_ActualizarStockDespuesDeMovimiento
-- Updates product stock after a movement is recorded.
-- Also checks for negative stock on outgoing movements.
CREATE TRIGGER `TRG_ActualizarStockDespuesDeMovimiento`
AFTER INSERT ON `MovimientosInventario`
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    DECLARE new_stock INT;

    -- Get current stock of the product
    SELECT `StockActual` INTO current_stock FROM `Productos` WHERE `ID_Producto` = NEW.ID_Producto;

    IF NEW.TipoMovimiento LIKE 'ENTRADA_%' OR NEW.TipoMovimiento = 'AJUSTE_POSITIVO' THEN
        SET new_stock = current_stock + NEW.Cantidad;
    ELSEIF NEW.TipoMovimiento LIKE 'SALIDA_%' OR NEW.TipoMovimiento = 'AJUSTE_NEGATIVO' THEN
        SET new_stock = current_stock - NEW.Cantidad;
        IF new_stock < 0 THEN
            -- Prevent stock from going negative.
            -- This will cause the original INSERT on MovimientosInventario to fail.
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Operación cancelada: Stock insuficiente.';
        END IF;
    ELSE
        -- Potentially handle other movement types or raise an error for unknown types
        SET new_stock = current_stock; -- Default to no change if type is unrecognized
    END IF;

    UPDATE `Productos`
    SET `StockActual` = new_stock,
        `UltimaActualizacionStock` = NEW.FechaMovimiento -- Use movement date
    WHERE `ID_Producto` = NEW.ID_Producto;
END$$


-- Trigger: TRG_LogCambiosPrecioProducto
-- Logs changes to PrecioCompra or PrecioVenta in Productos table.
CREATE TRIGGER `TRG_LogCambiosPrecioProducto`
AFTER UPDATE ON `Productos`
FOR EACH ROW
BEGIN
    IF OLD.PrecioCompra <> NEW.PrecioCompra OR OLD.PrecioVenta <> NEW.PrecioVenta THEN
        -- Attempt to get current user ID from a session variable (application needs to set this)
        -- For this example, we'll assume it's passed or use a placeholder if not available.
        -- A more robust solution involves passing UserID through the UPDATE statement or using context variables.
        -- For simplicity, if we can't get ID_Usuario that performed the update directly in trigger context,
        -- this part might need to be handled at application level or by ensuring SPs pass user ID.
        -- Here, we'll assume the application logic that calls the UPDATE on Productos
        -- would be responsible for identifying the user.
        -- If an SP updates it, the SP knows the user. If direct SQL, it's harder.
        -- Let's assume an SP_UpdateProductPrice would handle this and pass the user ID.
        -- For now, we'll insert a placeholder if no user context is available.
        -- This trigger is more effective if updates are done via SPs that log the user.
        
        -- We need ID_UsuarioModifico. The trigger itself doesn't know who ran the UPDATE statement
        -- unless the application sets a session variable like @current_user_id.
        -- For this example, we'll make it nullable or use a default if not set.
        -- The design document specifies ID_UsuarioModifico in LogPrecios, which is good.
        -- The challenge is getting it reliably into the trigger.
        -- For now, let's assume the application sets a session variable:
        -- SET @current_user_id = <user_id_from_session>; -- Before running the UPDATE
        -- UPDATE Productos SET ...
        
        INSERT INTO `LogPrecios` (
            `ID_Producto`,
            `PrecioCompraAnterior`,
            `PrecioCompraNuevo`,
            `PrecioVentaAnterior`,
            `PrecioVentaNuevo`,
            `ID_UsuarioModifico`,
            `FechaModificacion`
        )
        VALUES (
            NEW.ID_Producto,
            OLD.PrecioCompra,
            NEW.PrecioCompra,
            OLD.PrecioVenta,
            NEW.PrecioVenta,
            COALESCE(@current_user_id, 1), -- Fallback to user 1 (e.g., root) if session var not set. Needs improvement for real app.
            CURRENT_TIMESTAMP
        );
    END IF;
END$$

-- Trigger: TRG_PrevenirStockNegativo (Alternative/Refinement)
-- The logic for preventing negative stock is now primarily within TRG_ActualizarStockDespuesDeMovimiento.
-- A BEFORE UPDATE trigger on Productos could also be used if direct updates to StockActual are allowed
-- and need to be policed, but it's generally better to manage stock via MovimientosInventario.
--
-- CREATE TRIGGER `TRG_PrevenirStockNegativoEnProductos`
-- BEFORE UPDATE ON `Productos`
-- FOR EACH ROW
-- BEGIN
--    IF NEW.StockActual < 0 THEN
--        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Actualización cancelada: El stock no puede ser negativo.';
--    END IF;
-- END$$
-- This trigger (TRG_PrevenirStockNegativoEnProductos) would be useful if someone tries to
-- directly execute `UPDATE Productos SET StockActual = -5 WHERE ...`.
-- The current setup relies on `MovimientosInventario` and its trigger.

DELIMITER ;

-- -----------------------------------------------------
-- Stored Procedures
-- -----------------------------------------------------
DELIMITER $$

-- Stored Procedure: SP_RegistrarEntradaStock
-- Registers a new stock entry.
CREATE PROCEDURE `SP_RegistrarEntradaStock`(
    IN p_ID_Producto INT,
    IN p_Cantidad INT,
    IN p_ID_Usuario INT,
    IN p_TipoMovimiento VARCHAR(50), -- e.g., 'ENTRADA_COMPRA', 'AJUSTE_POSITIVO'
    IN p_PrecioUnitarioMovimiento DECIMAL(10,2),
    IN p_Notas TEXT
)
BEGIN
    DECLARE product_exists INT;

    -- Validate product existence
    SELECT COUNT(*) INTO product_exists FROM `Productos` WHERE `ID_Producto` = p_ID_Producto;

    IF product_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: El producto no existe.';
    ELSEIF p_Cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: La cantidad debe ser positiva para una entrada.';
    ELSE
        INSERT INTO `MovimientosInventario` (
            `ID_Producto`,
            `Cantidad`,
            `ID_Usuario`,
            `TipoMovimiento`,
            `PrecioUnitarioMovimiento`,
            `Notas`,
            `FechaMovimiento`
        ) VALUES (
            p_ID_Producto,
            p_Cantidad,
            p_ID_Usuario,
            p_TipoMovimiento, -- Ensure this is an "entrada" type
            p_PrecioUnitarioMovimiento,
            p_Notas,
            CURRENT_TIMESTAMP
        );
        -- The trigger TRG_ActualizarStockDespuesDeMovimiento will handle updating Productos.StockActual
        SELECT 'Entrada de stock registrada exitosamente.' AS Message;
    END IF;
END$$


-- Stored Procedure: SP_RegistrarSalidaStock
-- Registers a new stock withdrawal.
CREATE PROCEDURE `SP_RegistrarSalidaStock`(
    IN p_ID_Producto INT,
    IN p_Cantidad INT,
    IN p_ID_Usuario INT,
    IN p_TipoMovimiento VARCHAR(50), -- e.g., 'SALIDA_VENTA', 'AJUSTE_NEGATIVO'
    IN p_PrecioUnitarioMovimiento DECIMAL(10,2),
    IN p_Notas TEXT
)
BEGIN
    DECLARE product_exists INT;
    DECLARE current_stock_val INT;

    SELECT COUNT(*), `StockActual` INTO product_exists, current_stock_val
    FROM `Productos` WHERE `ID_Producto` = p_ID_Producto;

    IF product_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: El producto no existe.';
    ELSEIF p_Cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: La cantidad debe ser positiva para una salida.';
    -- The trigger will check for negative stock, but we can do a pre-check here too.
    -- ELSEIF current_stock_val < p_Cantidad AND (p_TipoMovimiento LIKE 'SALIDA_%' OR p_TipoMovimiento = 'AJUSTE_NEGATIVO') THEN
    --    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Stock insuficiente para la salida solicitada.';
    ELSE
        INSERT INTO `MovimientosInventario` (
            `ID_Producto`,
            `Cantidad`,
            `ID_Usuario`,
            `TipoMovimiento`,
            `PrecioUnitarioMovimiento`,
            `Notas`,
            `FechaMovimiento`
        ) VALUES (
            p_ID_Producto,
            p_Cantidad,
            p_ID_Usuario,
            p_TipoMovimiento, -- Ensure this is a "salida" type
            p_PrecioUnitarioMovimiento,
            p_Notas,
            CURRENT_TIMESTAMP
        );
        -- The trigger TRG_ActualizarStockDespuesDeMovimiento will handle updating StockActual and negative check.
        SELECT 'Salida de stock registrada exitosamente.' AS Message;
    END IF;
END$$


-- Stored Procedure: SP_AgregarNuevoProductoCompleto
-- Adds a new product to the catalog and an initial stock movement if applicable.
CREATE PROCEDURE `SP_AgregarNuevoProductoCompleto`(
    IN p_CodigoProducto VARCHAR(50),
    IN p_NombreProducto VARCHAR(150),
    IN p_DescripcionProducto TEXT,
    IN p_ID_Categoria INT,
    IN p_ID_Proveedor INT,
    IN p_PrecioCompra DECIMAL(10,2),
    IN p_PrecioVenta DECIMAL(10,2),
    IN p_StockInicial INT,
    IN p_StockMinimo INT,
    IN p_UnidadMedida VARCHAR(20),
    IN p_ID_Usuario_Registro INT -- User performing the addition
)
BEGIN
    DECLARE new_product_id INT;

    -- Insert the new product
    INSERT INTO `Productos` (
        `CodigoProducto`, `NombreProducto`, `DescripcionProducto`, `ID_Categoria`, `ID_Proveedor`,
        `PrecioCompra`, `PrecioVenta`, `StockActual`, `StockMinimo`, `UnidadMedida`, `FechaAgregado`, `UltimaActualizacionStock`
    ) VALUES (
        p_CodigoProducto, p_NombreProducto, p_DescripcionProducto, p_ID_Categoria, p_ID_Proveedor,
        p_PrecioCompra, p_PrecioVenta, 0, p_StockMinimo, p_UnidadMedida, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    );

    SET new_product_id = LAST_INSERT_ID();

    -- If initial stock is provided, register it as an initial entry movement
    IF p_StockInicial > 0 THEN
        INSERT INTO `MovimientosInventario` (
            `ID_Producto`, `TipoMovimiento`, `Cantidad`, `ID_Usuario`, `PrecioUnitarioMovimiento`, `Notas`, `FechaMovimiento`
        ) VALUES (
            new_product_id, 'ENTRADA_INICIAL', p_StockInicial, p_ID_Usuario_Registro, p_PrecioCompra, 'Stock inicial al agregar producto', CURRENT_TIMESTAMP
        );
        -- The trigger TRG_ActualizarStockDespuesDeMovimiento will update StockActual in Productos
    END IF;

    SELECT new_product_id AS NewProductID, 'Producto agregado exitosamente.' AS Message;
END$$


-- Stored Procedure: SP_ObtenerProductosConBajoStock
-- Retrieves products that are at or below their minimum stock level.
CREATE PROCEDURE `SP_ObtenerProductosConBajoStock`(
    IN p_UmbralPorcentajeAdicional INT -- Optional: e.g., 10 for 10% above minimum stock
)
BEGIN
    IF p_UmbralPorcentajeAdicional IS NULL OR p_UmbralPorcentajeAdicional <= 0 THEN
        SELECT
            P.ID_Producto,
            P.CodigoProducto,
            P.NombreProducto,
            C.NombreCategoria,
            P.StockActual,
            P.StockMinimo,
            P.UnidadMedida,
            Prov.NombreProveedor
        FROM `Productos` P
        JOIN `Categorias` C ON P.ID_Categoria = C.ID_Categoria
        LEFT JOIN `Proveedores` Prov ON P.ID_Proveedor = Prov.ID_Proveedor
        WHERE P.StockActual <= P.StockMinimo
        ORDER BY P.NombreProducto;
    ELSE
        SELECT
            P.ID_Producto,
            P.CodigoProducto,
            P.NombreProducto,
            C.NombreCategoria,
            P.StockActual,
            P.StockMinimo,
            (P.StockMinimo + (P.StockMinimo * p_UmbralPorcentajeAdicional / 100.0)) AS UmbralConsiderado,
            P.UnidadMedida,
            Prov.NombreProveedor
        FROM `Productos` P
        JOIN `Categorias` C ON P.ID_Categoria = C.ID_Categoria
        LEFT JOIN `Proveedores` Prov ON P.ID_Proveedor = Prov.ID_Proveedor
        WHERE P.StockActual <= (P.StockMinimo + (P.StockMinimo * p_UmbralPorcentajeAdicional / 100.0))
        ORDER BY P.NombreProducto;
    END IF;
END$$


-- Stored Procedure: SP_ConsultarStockProducto
-- Retrieves stock information for a specific product by ID or Code.
CREATE PROCEDURE `SP_ConsultarStockProducto`(
    IN p_IdentificadorProducto VARCHAR(50) -- Can be ID_Producto or CodigoProducto
)
BEGIN
    SELECT
        P.ID_Producto,
        P.CodigoProducto,
        P.NombreProducto,
        P.StockActual,
        P.UnidadMedida,
        C.NombreCategoria,
        P.PrecioVenta
    FROM `Productos` P
    JOIN `Categorias` C ON P.ID_Categoria = C.ID_Categoria
    WHERE P.ID_Producto = p_IdentificadorProducto OR P.CodigoProducto = p_IdentificadorProducto;
END$$

DELIMITER ;

-- Example calls for Stored Procedures:

-- SET @current_user_id = 1; -- Example: Root user performing action (for TRG_LogCambiosPrecioProducto)

-- CALL SP_RegistrarEntradaStock(1, 10, 1, 'ENTRADA_COMPRA', 790.00, 'Nueva compra de laptops');
-- CALL SP_RegistrarSalidaStock(2, 5, 2, 'SALIDA_VENTA', 25.00, 'Venta a cliente minorista');

-- CALL SP_AgregarNuevoProductoCompleto(
--    'ALIM003', 'Atún en Lata', 'Atún en aceite, lata 150g', 3, 3,
--    0.50, 1.00, 200, 50, 'unidad', 1
-- );

-- CALL SP_ObtenerProductosConBajoStock(NULL); -- Products at or below minimum
-- CALL SP_ObtenerProductosConBajoStock(20); -- Products <= 120% of minimum stock

-- CALL SP_ConsultarStockProducto('ELEC001');
-- CALL SP_ConsultarStockProducto('3'); -- Assuming ID_Producto 3 exists

-- To show data for user view (as per document)
-- Tabla 1: Productos y Stock Actual
SELECT P.CodigoProducto, P.NombreProducto, C.NombreCategoria, P.StockActual, P.PrecioVenta
FROM Productos P
JOIN Categorias C ON P.ID_Categoria = C.ID_Categoria
LIMIT 10;

-- Tabla 2: Últimos Movimientos Registrados por el Usuario (e.g., user with ID_Usuario = 2)
SELECT MI.FechaMovimiento, P.NombreProducto, MI.Cantidad, MI.TipoMovimiento
FROM MovimientosInventario MI
JOIN Productos P ON MI.ID_Producto = P.ID_Producto
WHERE MI.ID_Usuario = 2 -- Assuming user ID 2 is the logged-in 'usuario'
ORDER BY MI.FechaMovimiento DESC
LIMIT 10;

-- Tabla 3: Listado de Categorías de Productos
SELECT NombreCategoria, DescripcionCategoria
FROM Categorias
LIMIT 10;

