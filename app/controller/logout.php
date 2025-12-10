<?php
// logout.php
// Inicia la sesión para poder acceder a las variables de sesión.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destruye todas las variables de sesión.
$_SESSION = array();

// Si se desea destruir la sesión completamente, borra también la cookie de sesión.
// Nota: ¡Esto destruirá la sesión, y no solo los datos de la sesión!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión.
session_destroy();

// Redirige al usuario a la página de login (o a la página de inicio que prefieras).
// Asegúrate de que la ruta a login.php sea correcta desde la raíz de tu proyecto.
// Usamos "./" para asegurar que es relativo al directorio actual (raíz del proyecto).
header("Location: ./login.php?mensaje=Has cerrado sesión correctamente.");
exit();
?>
