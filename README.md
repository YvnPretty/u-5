# Sistema de Inventario (U-5)

Este es un sistema de gestión de inventario desarrollado en PHP con MySQL. Permite administrar productos, controlar stock y gestionar usuarios mediante un sistema de login seguro.

## 🚀 Características

- **Gestión de Usuarios**: Registro y Login seguro (contraseñas encriptadas).
- **CRUD de Productos**:
  - Agregar nuevos productos.
  - Ver lista de inventario.
  - Editar detalles y precios.
  - Eliminar productos.
- **Interfaz Responsiva**: Diseño limpio y adaptable usando CSS personalizado.
- **Seguridad**: Protección de rutas, validación de sesiones y consultas preparadas (PDO) para prevenir inyecciones SQL.

## ⚙️ Instalación y Configuración

1. **Clonar el repositorio**:
   ```bash
   git clone https://github.com/YvnPretty/u-5.git
   cd u-5
   ```

2. **Base de Datos**:
   - Importa el archivo SQL ubicado en `db/database.sql` en tu servidor MySQL (puedes usar phpMyAdmin o Workbench).
   - Esto creará las tablas necesarias (`t_usuario`, `t_inventario`, etc.).

3. **Configuración de Conexión**:
   - Navega a la carpeta `app/config/`.
   - Renombra el archivo `conexion.example.php` a `conexion.php`.
   - Edita `conexion.php` con tus credenciales de base de datos:
     ```php
     define("SERVIDOR", "localhost");
     define("USUARIO", "tu_usuario"); // Ej: root
     define("PASSWORD", "tu_contraseña");
     define("BASE_DATOS", "topicos"); // Asegúrate que coincida con tu DB
     define("PUERTO", "3306");
     ```

4. **Ejecutar**:
   - Abre tu navegador y accede a la carpeta del proyecto en tu servidor local (ej: `http://localhost/u-5/`).
   - Regístrate con un nuevo usuario o inicia sesión si ya tienes uno.

## 📂 Estructura del Proyecto

- `app/`: Contiene la lógica del negocio (controladores) y configuración.
  - `config/`: Archivos de conexión a BD.
  - `controller/`: Scripts que procesan los formularios (Login, Registro, CRUD).
- `components/`: Fragmentos de código reutilizables (Navbar, Footer).
- `public/`: Archivos estáticos (CSS, JS, Imágenes).
- `db/`: Scripts SQL para la base de datos.
- `_old_files/`: Archivos antiguos o de respaldo (no esenciales para el funcionamiento actual).

## 🛠️ Tecnologías

- PHP 8+
- MySQL / MariaDB
- HTML5 / CSS3
- JavaScript
