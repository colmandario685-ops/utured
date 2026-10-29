<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$mensaje = '';
$tipo_mensaje = '';

// Procesar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $fecha_nac = trim($_POST['fecha_nac'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    if (empty($nombre) || empty($fecha_nac)) {
        $mensaje = 'El nombre completo y la fecha de nacimiento son obligatorios.';
        $tipo_mensaje = 'error';
    } else {
        // Actualiza o limpia (si se envían vacíos) los campos
        $stmtUpdate = $pdo->prepare("
            UPDATE usuarios 
            SET nombre_completo = ?, fecha_nacimiento = ?, telefono = ?, direccion = ?
            WHERE id_usuario = ?
        ");
        
        $telefonoVal = !empty($telefono) ? $telefono : NULL;
        $direccionVal = !empty($direccion) ? $direccion : NULL;

        if ($stmtUpdate->execute([$nombre, $fecha_nac, $telefonoVal, $direccionVal, $id_usuario])) {
            $_SESSION['nombre'] = $nombre;
            header("Location: perfil.php");
            exit();
        } else {
            $mensaje = 'Ocurrió un error al guardar los cambios.';
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener datos actuales del usuario
$stmt = $pdo->prepare("SELECT nombre_completo, fecha_nacimiento, telefono, direccion, correo, ci FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$user = $stmt->fetch();

$nombre_val = $user['nombre_completo'] ?? '';
$fecha_nac_val = $user['fecha_nacimiento'] ?? '';
$telefono_val = $user['telefono'] ?? '';
$direccion_val = $user['direccion'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Editar Perfil</title>
    <link rel="stylesheet" href="perfil.css">
</head>
<body>

    <!-- Topbar superior -->
    <header class="topbar">
        <div class="logo-section">
            <div class="logo-icon">UTU</div>
            <div class="logo-text">
                <span class="brand-title">INSTITUCIÓN EDUCATIVA</span>
                <span class="brand-sub">Formamos para el futuro</span>
            </div>
        </div>
        <nav class="nav-links">
        <a href="index.php">Inicio</a>
            <a href="eventos.php">Eventos</a>
            <a href="noticias.php">Noticias</a>
            <a href="horarios.php">Horarios</a>
            <a href="ayuda.php">Ayuda</a>
        </nav>
        <div class="user-dropdown">
            <span class="user-icon">👤</span>
            <span class="user-name">Estudiante: <strong><?php echo htmlspecialchars($nombre_val); ?></strong></span>
        </div>
    </header>

    <div class="layout-container">
        <!-- Sidebar Izquierda -->
        <aside class="sidebar">
            <div class="sidebar-title">
                <span class="icon">🎓</span>
                <h3>Área de Estudiantes</h3>
            </div>
            <nav class="sidebar-menu">
                <a href="perfil.php" class="active"><span class="menu-icon">👤</span> Mi perfil</a>
                <a href="index.php"><span class="menu-icon">🎓</span> Mis datos académicos</a>
            </nav>
            <a href="auth_controller.php?action=logout" class="logout-link"><span class="menu-icon">↪️</span> Cerrar sesión</a>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="content-header">
                <h1>Editar Información del Perfil</h1>
                <p>Modifica o elimina tus datos de contacto personales.</p>
            </header>

            <?php if (!empty($mensaje)): ?>
                <div class="alert-box <?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <section class="edit-card">
                <form action="editar_perfil.php" method="POST" class="form-grid">
                    
                    <div class="form-group full-width">
                        <label for="nombre">Nombre Completo *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre_val); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_nac">Fecha de Nacimiento *</label>
                        <input type="date" id="fecha_nac" name="fecha_nac" value="<?php echo htmlspecialchars($fecha_nac_val); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono (Deja en blanco para eliminar)</label>
                        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono_val); ?>" placeholder="Ej: 099 123 456">
                    </div>

                    <div class="form-group full-width">
                        <label for="direccion">Dirección Residencial (Deja en blanco para eliminar)</label>
                        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion_val); ?>" placeholder="Ej: Av. Principal 1234">
                    </div>

                    <div class="form-actions-bar">
                        <a href="perfil.php" class="btn-cancel-link">Cancelar</a>
                        <button type="submit" class="btn-save-submit">💾 Guardar Cambios</button>
                    </div>
                </form>
            </section>
        </main>
    </div>

</body>
</html>