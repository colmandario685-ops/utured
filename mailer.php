<?php
/**
 * Función para enviar correos electrónicos de recuperación de contraseña.
 * Puedes adaptarla para usar mail() nativo de PHP o PHPMailer / SendGrid.
 */
function enviarCorreoRecuperacion($correoDestino, $nombreUsuario, $enlaceRecuperacion) {
    $asunto = "UTUred - Recuperación de Contraseña";
    
    $mensaje = "Hola " . htmlspecialchars($nombreUsuario) . ",\n\n";
    $mensaje .= "Has solicitado restablecer tu contraseña en UTUred.\n";
    $mensaje .= "Haz clic en el siguiente enlace para continuar:\n";
    $mensaje .= $enlaceRecuperacion . "\n\n";
    $mensaje .= "Si no solicitaste este cambio, puedes ignorar este correo.\n";
    
    $headers = "From: no-reply@utu.edu.uy\r\n";
    $headers .= "Reply-To: no-reply@utu.edu.uy\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Intenta enviar el correo nativo
    return @mail($correoDestino, $asunto, $mensaje, $headers);
}
?>