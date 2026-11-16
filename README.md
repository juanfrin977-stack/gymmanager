# GymManager

Aplicación web para gestionar las reservas de clases de un gimnasio, hecha con PHP y MySQL.

## Funcionalidades

- Registro e inicio de sesión de usuarios (contraseñas cifradas con `password_hash`)
- Listado de clases y horarios
- Reservar, editar y cancelar reservas (`mis_reservas.php`)
- Formulario de contacto que guarda los mensajes en la base de datos

## Cómo ejecutarlo en local (XAMPP)

1. Copia esta carpeta en `htdocs/gymmanager`.
2. En el XAMPP Control Panel, arranca **Apache** y **MySQL**.
3. En phpMyAdmin (`http://localhost/phpmyadmin`), crea la base de datos `gymmanager` e importa `database/gymmanager.sql`.
4. Abre `http://localhost/gymmanager/`.

La conexión se configura en `conexion.php` (por defecto `root` sin contraseña, lo habitual en XAMPP).

Los usuarios de ejemplo de la base de datos son de prueba (`@test.com`).
