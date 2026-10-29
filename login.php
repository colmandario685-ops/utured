<?php
session_start();
// Si el usuario ya está autenticado, lo redirigimos al panel principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Iniciar Sesión / Registro</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="brand-section">
            <div class="logo">
                <h1>UTUred</h1>
                <p>Plataforma Educativa Institucional</p>
            </div>
            <p class="tagline">Toda la información que necesitas en un solo lugar.</p>
        </div>

        <div class="form-section">
            <!-- Pestañas para alternar -->
            <div class="auth-tabs">
                <button id="tabLogin" class="tab-btn active" onclick="showForm('login')">Iniciar Sesión</button>
                <button id="tabRegister" class="tab-btn" onclick="showForm('register')">Registrarse</button>
            </div>

            <!-- Formularios -->
            <div class="forms-wrapper">
                <!-- FORMULARIO LOGIN -->
                <form id="formLogin" action="auth_controller.php?action=login" method="POST" class="auth-form">
                    <h2>Bienvenido de nuevo</h2>
                    
                    <div class="input-group">
                        <label for="login_correo">Correo electrónico</label>
                        <input type="email" id="login_correo" name="correo" placeholder="ejemplo@utu.edu.uy" required>
                    </div>

                    <div class="input-group">
                        <label for="login_password">Contraseña</label>
                        <input type="password" id="login_password" name="password" placeholder="••••••••" required>
                    </div>

                    <div class="form-actions">
                        <a href="#" id="btnOpenRecovery" class="forgot-pass">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-submit">Iniciar sesión</button>
                </form>

                <!-- FORMULARIO REGISTRO -->
                <form id="formRegister" action="auth_controller.php?action=register" method="POST" class="auth-form hidden">
                    <h2>Crear una cuenta</h2>

                    <div class="input-group">
                        <label for="reg_nombre">Nombre completo</label>
                        <input type="text" id="reg_nombre" name="nombre_completo" placeholder="Ej: Juan Pérez" required>
                    </div>

                    <div class="input-group">
                        <label for="reg_ci">Cédula de Identidad</label>
                        <input type="text" id="reg_ci" name="ci" placeholder="5.123.456-7" required>
                    </div>

                    <div class="input-group">
                        <label for="reg_correo">Correo institucional</label>
                        <input type="email" id="reg_correo" name="correo" placeholder="ejemplo@utu.edu.uy" required>
                    </div>

                    <div class="input-group">
                        <label for="reg_password">Contraseña</label>
                        <input type="password" id="reg_password" name="password" minlength="6" placeholder="Mínimo 6 caracteres" required>
                    </div>

                    <div class="input-group">
                        <label for="reg_fecha">Fecha de nacimiento</label>
                        <input type="date" id="reg_fecha" name="fecha_nacimiento" required>
                    </div>

                    <button type="submit" class="btn-submit">Registrarse</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para recuperación de contraseña -->
    <div id="recoveryModal" class="modal hidden">
        <div class="modal-content">
            <h3>Recuperar contraseña</h3>
            <p>Ingresa tu correo y te enviaremos las instrucciones de restablecimiento.</p>
            <form id="formRecovery" action="auth_controller.php?action=recovery" method="POST">
                <input type="email" name="correo" placeholder="ejemplo@utu.edu.uy" required>
                <div class="modal-buttons">
                    <button type="submit" class="btn-submit">Enviar instrucciones</button>
                    <button type="button" id="btnCloseRecovery" class="btn-cancel">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
       <script src="auth.js"></script>
</body>
</html>