<?php
session_start();
require_once 'db.php'; // Asegúrate de que este archivo conecta correctamente con PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y limpiar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // Validar campos vacíos
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        header("Location: horarios.php?error=campos_vacios");
        exit();
    }

    // Validar formato de correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: horarios.php?error=email_invalido");
        exit();
    }

    try {
        // Opción A: Guardar el mensaje en la base de datos
        $stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $asunto, $mensaje]);

        /* 
           Opción B (Alternativa/Adicional): Enviar un correo electrónico institucional 
           usando la función mail() de PHP o PHPMailer.
           
           $para = "escuelatecnicadeguichon@gmail.com";
           $cuerpo = "Mensaje de: $nombre ($email)\nAsunto: $asunto\n\nMensaje:\n$mensaje";
           $cabeceras = "From: $email";
           mail($para, "Contacto Web: $asunto", $cuerpo, $cabeceras);
        */

        // Redirigir de vuelta a horarios con éxito (puedes activar la pestaña de atención por URL o JS)
        header("Location: horarios.php?enviado=1");
        exit();

    } catch (PDOException $e) {
        // Manejo de errores de base de datos
        header("Location: horarios.php?error=db_fallo");
        exit();
    }
} else {
    // Si intentan acceder directamente por la URL sin enviar el formulario
    header("Location: horarios.php");
    exit();
}