<?php
session_start();
require_once 'db.php';

// Verificación de sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Prevenir caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$id_estudiante = $_SESSION['usuario_id'];

// Consultar datos personales y académicos del estudiante
$stmtUser = $pdo->prepare("
    SELECT u.nombre_completo, u.ci, u.correo, u.fecha_nacimiento, u.telefono, u.direccion,
           g.nivel, g.grupo, g.anio_lectivo
    FROM usuarios u
    LEFT JOIN estudiante_grupo eg ON u.id_usuario = eg.id_estudiante
    LEFT JOIN grupos g ON eg.id_grupo = g.id_grupo
    WHERE u.id_usuario = ?
");
$stmtUser->execute([$id_estudiante]);
$estudiante = $stmtUser->fetch();

$nombre = $estudiante['nombre_completo'] ?? $_SESSION['nombre'];
$ci = $estudiante['ci'] ?? '4.123.456-7';
$correo = $estudiante['correo'] ?? 'juan.perez@institucion.edu';
$fecha_nac_raw = $estudiante['fecha_nacimiento'] ?? '';
$fecha_nac = !empty($fecha_nac_raw) ? date("d/m/Y", strtotime($fecha_nac_raw)) : 'Sin registrar';
$telefono = !empty($estudiante['telefono']) ? $estudiante['telefono'] : 'Sin registrar';
$direccion = !empty($estudiante['direccion']) ? $estudiante['direccion'] : 'Sin registrar';
$nivel = $estudiante['nivel'] ?? '3° Bachillerato';
$grupo = $estudiante['grupo'] ?? '3° B';
$anio = $estudiante['anio_lectivo'] ?? '2026';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Mi Perfil</title>
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
            <span class="user-name">Estudiante: <strong><?php echo htmlspecialchars($nombre); ?></strong></span>
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
                <a href="#"><span class="menu-icon">✉️</span> Mis mensajes</a>
                <a href="#"><span class="menu-icon">📄</span> Mis documentos</a>
                <a href="#"><span class="menu-icon">⚙️</span> Configuración</a>
                <!-- Enlace de Cerrar Sesión dirigiéndose al controlador -->
                <a href="auth_controller.php?action=logout" class="logout">Cerrar sesión</a>
                </nav>
            <a href="auth_controller.php?action=logout" class="logout-link"><span class="menu-icon">↪️</span> Cerrar sesión</a>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="content-header">
                <h1>Mi perfil</h1>
                <p>Consulta y actualiza tu información personal.</p>
            </header>

            <!-- Card Perfil Personal -->
            <section class="profile-card">
                <div class="avatar-container">
                    <div class="avatar-circle">
                        <span class="avatar-icon">👤</span>
                        <button class="btn-camera" title="Cambiar foto">📷</button>
                    </div>
                </div>

                <div class="profile-details-grid">
                    <div class="detail-column">
                        <div class="detail-item">
                            <span class="label">Nombre completo</span>
                            <span class="value main-name"><?php echo htmlspecialchars($nombre); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Fecha de nacimiento</span>
                            <span class="value"><?php echo htmlspecialchars($fecha_nac); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Correo institucional</span>
                            <span class="value email"><?php echo htmlspecialchars($correo); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Teléfono</span>
                            <span class="value"><?php echo htmlspecialchars($telefono); ?></span>
                        </div>
                    </div>

                    <div class="detail-column">
                        <div class="detail-item">
                            <span class="label">Documento de identidad</span>
                            <span class="value"><?php echo htmlspecialchars($ci); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Dirección</span>
                            <span class="value"><?php echo htmlspecialchars($direccion); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Fecha de ingreso</span>
                            <span class="value">10 de marzo de 2023</span>
                        </div>
                    </div>
                </div>

                <!-- Botón de enlace hacia la nueva página de edición -->
                <div class="edit-action">
                    <a href="editar_perfil.php" class="btn-edit">✏️ Editar perfil</a>
                </div>
            </section>

            <!-- Cuadrícula Inferior de Información -->
            <div class="sections-grid">
                <div class="grid-left">
                    <section class="info-block">
                        <div class="block-header">
                            <h3>Mis datos académicos</h3>
                            <p>Información sobre tu curso y rendimiento académico.</p>
                        </div>
                        <div class="academic-cards-row">
                            <div class="acad-card">
                                <span class="acad-icon blue">🎓</span>
                                <div>
                                    <span class="acad-label">Grado / Curso</span>
                                    <strong><?php echo htmlspecialchars($nivel); ?></strong>
                                </div>
                            </div>
                            <div class="acad-card">
                                <span class="acad-icon blue">📅</span>
                                <div>
                                    <span class="acad-label">Período académico</span>
                                    <strong><?php echo htmlspecialchars($anio); ?></strong>
                                </div>
                            </div>
                            <div class="acad-card">
                                <span class="acad-icon blue">👥</span>
                                <div>
                                    <span class="acad-label">Grupo</span>
                                    <strong><?php echo htmlspecialchars($grupo); ?></strong>
                                </div>
                            </div>
                            <div class="acad-card">
                                <span class="acad-icon blue">👤</span>
                                <div>
                                    <span class="acad-label">Tutor</span>
                                    <strong>Prof. Ana López</strong>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

</body>
</html>