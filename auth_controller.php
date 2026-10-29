<?php
session_start();
require_once 'db.php';     // Conexión a la base de datos 'utured'
require_once 'mailer.php'; // Función para envío de correos por SMTP

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        procesarLogin($pdo);
        break;
    case 'register':
        procesarRegistro($pdo);
        break;
    case 'recovery':
        procesarRecuperacion($pdo);
        break;
    case 'logout':
        procesarLogout();
        break;
    default:
        header("Location: login.php");
        exit();
}

/**
 * Procesa el inicio de sesión del usuario
 */
function procesarLogin($pdo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $correo = trim($_POST['correo']);
        $password = trim($_POST['password']);

        if (empty($correo) || empty($password)) {
            echo "<script>alert('Por favor, completa todos los campos.'); window.location.href='login.php';</script>";
            exit();
        }

        // Consultar el usuario por correo
        $stmt = $pdo->prepare("SELECT id_usuario, nombre_completo, password_hash, rol FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        // Comparar contraseña mediante hash SHA-256
        if ($usuario && hash('sha256', $password) === $usuario['password_hash']) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre_completo'];
            $_SESSION['rol']        = $usuario['rol'];

            header("Location: index.php");
            exit();


            
        } else {
            echo "<script>alert('Correo electrónico o contraseña incorrectos.'); window.location.href='login.php';</script>";
            exit();
        }
    }
}

/**
 * Procesa el registro de un nuevo estudiante y le asigna el grupo/resumen por defecto
 */
function procesarRegistro($pdo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre    = trim($_POST['nombre_completo']);
        $ci        = trim($_POST['ci']);
        $correo    = trim($_POST['correo']);
        $password  = trim($_POST['password']);
        $fecha_nac = $_POST['fecha_nacimiento'];

        if (empty($nombre) || empty($ci) || empty($correo) || empty($password) || empty($fecha_nac)) {
            echo "<script>alert('Por favor, completa todos los campos obligatorios.'); window.location.href='login.php';</script>";
            exit();
        }

        $passHash = hash('sha256', $password);

        try {
            $pdo->beginTransaction();

            // 1. Insertar el nuevo usuario en la tabla 'usuarios'
            $stmt = $pdo->prepare("INSERT INTO usuarios (ci, nombre_completo, correo, password_hash, rol, fecha_nacimiento) VALUES (?, ?, ?, ?, 'estudiante', ?)");
            $stmt->execute([$ci, $nombre, $correo, $passHash, $fecha_nac]);
            $id_nuevo_usuario = $pdo->lastInsertId();

            // 2. Inicializar el resumen académico en 'resumen_academico'
            $stmtResumen = $pdo->prepare("INSERT INTO resumen_academico (id_estudiante, promedio_general, asistencia_porcentaje, conducta) VALUES (?, 0.0, 100, 'A')");
            $stmtResumen->execute([$id_nuevo_usuario]);

            // 3. Asignar al grupo por defecto (ID: 1 -> 3° Bachillerato 3° B)
            $stmtGrupo = $pdo->prepare("INSERT INTO estudiante_grupo (id_estudiante, id_grupo) VALUES (?, 1)");
            $stmtGrupo->execute([$id_nuevo_usuario]);

            $pdo->commit();

            echo "<script>alert('Registro exitoso. Ya puedes iniciar sesión con tu correo y contraseña.'); window.location.href='login.php';</script>";
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<script>alert('Error al registrar: La cédula o el correo electrónico ya se encuentran registrados.'); window.location.href='login.php';</script>";
            exit();
        }
    }
}

/**
 * Genera el token de restablecimiento y envía el correo por SMTP
 */
function procesarRecuperacion($pdo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $correo = trim($_POST['correo']);

        if (empty($correo)) {
            echo "<script>alert('Por favor, ingresa tu correo electrónico.'); window.location.href='login.php';</script>";
            exit();
        }

        // Verificar si el usuario existe
        $stmt = $pdo->prepare("SELECT id_usuario, nombre_completo FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Generar un token único y vigencia de 1 hora
            $token = bin2hex(random_bytes(32));
            $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Guardar token en 'recuperacion_password'
            $stmtInsert = $pdo->prepare("INSERT INTO recuperacion_password (correo, token, expiracion) VALUES (?, ?, ?)");
            $stmtInsert->execute([$correo, $token, $expiracion]);

            // Enlace hacia la vista de cambio de contraseña
            $link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;

            // Enviar correo mediante SMTP PHPMailer
            if (enviarCorreoRecuperacion($correo, $usuario['nombre_completo'], $link)) {
                echo "<script>alert('Hemos enviado un correo con las instrucciones para restablecer tu contraseña.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Ocurrió un error al enviar el correo. Por favor, inténtalo más tarde.'); window.location.href='login.php';</script>";
            }
            exit();
        } else {
            // Mensaje genérico por motivos de seguridad
            echo "<script>alert('Si el correo está registrado, recibirás un mensaje con las instrucciones en breve.'); window.location.href='login.php';</script>";
            exit();
        }
    }
}

/**
 * Destruye la sesión del usuario y redirige al inicio
 */
function procesarLogout() {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    header("Location: login.php");
    exit();
}
?>