<?php
session_start();
require_once 'db.php';
require_once 'mailer.php';

// Seguridad: Solo administradores
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'eliminar_usuario':
        procesarEliminacionUsuario($pdo);
        break;
    case 'responder_mensaje':
        procesarRespuestaMensaje();
        break;
    default:
        header("Location: admin_dashboard.php");
        exit();
}

function procesarEliminacionUsuario($pdo) {
    $id = $_GET['id'] ?? null;
    $id_admin_actual = $_SESSION['usuario_id'];

    if (!$id || $id == $id_admin_actual) {
        echo "<script>alert('Acción no permitida.'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Limpiar registros en tablas dependientes para evitar errores de clave foránea
        $pdo->prepare("DELETE FROM estudiante_grupo WHERE id_estudiante = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM resumen_academico WHERE id_estudiante = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM calificaciones WHERE id_estudiante = ?")->execute([$id]);

        // Eliminar el usuario
        $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?")->execute([$id]);

        $pdo->commit();
        echo "<script>alert('Usuario eliminado correctamente.'); window.location.href='admin_dashboard.php';</script>";
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<script>alert('Error al eliminar el usuario.'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }
}

function procesarRespuestaMensaje() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email_destino'] ?? '');
        $nombre = trim($_POST['nombre_destino'] ?? '');
        $respuesta = trim($_POST['respuesta'] ?? '');

        if (empty($email) || empty($respuesta)) {
            echo "<script>alert('El mensaje de respuesta no puede estar vacío.'); window.location.href='admin_dashboard.php';</script>";
            exit();
        }

        $asuntoEmail = "Respuesta a su consulta institucional - UTUred";
        $cuerpoEmail = "Hola $nombre,\n\nHemos recibido tu consulta a través de la plataforma UTUred. Aquí tienes la respuesta de la institución:\n\n$respuesta\n\nAtentamente,\nEquipo de Administración - Escuela Técnica de Guichón";

        $enviado = false;
        if (function_exists('enviarCorreoRecuperacion')) {
            $enviado = enviarCorreoRecuperacion($email, $nombre, $cuerpoEmail);
        } else {
            $headers = "From: escuelatecnicadeguichon@gmail.com\r\nContent-Type: text/plain; charset=UTF-8";
            $enviado = mail($email, $asuntoEmail, $cuerpoEmail, $headers);
        }

        if ($enviado) {
            echo "<script>alert('¡Respuesta enviada exitosamente al correo del usuario!'); window.location.href='admin_dashboard.php';</script>";
        } else {
            echo "<script>alert('No se pudo enviar el correo electrónico. Inténtalo de nuevo.'); window.location.href='admin_dashboard.php';</script>";
        }
        exit();
    }
}
?>