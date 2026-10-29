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

// Obtener datos del usuario y grupo
$stmtUser = $pdo->prepare("
    SELECT u.nombre_completo, g.nivel, g.grupo, g.anio_lectivo,
           ra.promedio_general, ra.asistencia_porcentaje
    FROM usuarios u
    LEFT JOIN estudiante_grupo eg ON u.id_usuario = eg.id_estudiante
    LEFT JOIN grupos g ON eg.id_grupo = g.id_grupo
    LEFT JOIN resumen_academico ra ON u.id_usuario = ra.id_estudiante
    WHERE u.id_usuario = ?
");
$stmtUser->execute([$id_estudiante]);
$datos = $stmtUser->fetch();

$nombre_usuario = $datos['nombre_completo'] ?? $_SESSION['nombre'];
$nivel = $datos['nivel'] ?? '3° Bachillerato';
$grupo = $datos['grupo'] ?? '3° B';
$anio = $datos['anio_lectivo'] ?? '2026';
$promedio = $datos['promedio_general'] ?? 8.6;
$asistencia = $datos['asistencia_porcentaje'] ?? 92;

// Obtener materias y calificaciones
$stmtMaterias = $pdo->prepare("
    SELECT m.nombre AS materia, u.nombre_completo AS docente, c.promedio AS calificacion, c.estado
    FROM calificaciones c
    JOIN materias m ON c.id_materia = m.id_materia
    LEFT JOIN usuarios u ON m.docente_id = u.id_usuario
    WHERE c.id_estudiante = ?
");
$stmtMaterias->execute([$id_estudiante]);
$materias = $stmtMaterias->fetchAll();

// Si no existen materias registradas en BD para este alumno, mostramos las por defecto de la interfaz
if (empty($materias)) {
    $materias = [
        ['materia' => 'Lengua y Literatura', 'docente' => 'Prof. Ana López', 'calificacion' => '9.0', 'estado' => 'Aprobado'],
        ['materia' => 'Matemática', 'docente' => 'Prof. Carlos Ramírez', 'calificacion' => '8.2', 'estado' => 'Aprobado'],
        ['materia' => 'Química', 'docente' => 'Prof. María González', 'calificacion' => '8.5', 'estado' => 'Aprobado'],
        ['materia' => 'Historia', 'docente' => 'Prof. José Martínez', 'calificacion' => '8.0', 'estado' => 'Aprobado'],
        ['materia' => 'Educación Física', 'docente' => 'Prof. Laura Silva', 'calificacion' => '9.2', 'estado' => 'Aprobado']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Nivel Académico</title>
    <link rel="stylesheet" href="nivel_academico.css">
</head>
<body>

    <!-- Header superior -->
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
            <a href="#">Eventos</a>
            <a href="noticias.php">Noticias</a>
            <a href="horarios.php">Horarios</a>
            <a href="ayuda.php">Ayuda</a>
        </nav>
        <div class="user-dropdown">
            <span class="user-icon">👤</span>
            <span class="user-name">Estudiante: <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></span>
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
                <a href="perfil.php"><span class="menu-icon">👤</span> Mi perfil</a>
                <a href="nivel_academico.php" class="active"><span class="menu-icon">🎓</span> Nivel académico</a>
                <a href="index.php"><span class="menu-icon">📊</span> Mis datos académicos</a>
                <a href="#"><span class="menu-icon">✉️</span> Mis mensajes</a>
                <a href="#"><span class="menu-icon">📄</span> Mis documentos</a>
                <a href="#"><span class="menu-icon">⚙️</span> Configuración</a>
            </nav>
            <a href="auth_controller.php?action=logout" class="logout-link"><span class="menu-icon">↪️</span> Cerrar sesión</a>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="content-header">
                <h1>Nivel académico</h1>
                <p>Consulta la información de tu nivel académico actual y tu progreso.</p>
            </header>

            <!-- Card Banner Superior -->
            <section class="level-banner-card">
                <div class="banner-main">
                    <div class="level-icon-wrapper">
                        🎓
                    </div>
                    <div class="level-info">
                        <div class="level-title-row">
                            <h2><?php echo htmlspecialchars($nivel); ?></h2>
                            <span class="badge-status">En curso</span>
                        </div>
                        <p>Estás cursando el último año de bachillerato. ¡Sigue así, vas por un gran camino!</p>
                    </div>
                </div>

                <div class="banner-details">
                    <div class="detail-box">
                        <span class="box-icon">📅</span>
                        <div>
                            <span class="box-label">Año académico</span>
                            <strong><?php echo htmlspecialchars($anio); ?></strong>
                        </div>
                    </div>
                    <div class="detail-box">
                        <span class="box-icon">👥</span>
                        <div>
                            <span class="box-label">Grupo</span>
                            <strong><?php echo htmlspecialchars($grupo); ?></strong>
                        </div>
                    </div>
                    <div class="detail-box">
                        <span class="box-icon">👤</span>
                        <div>
                            <span class="box-label">Tutor</span>
                            <strong>Prof. Ana López</strong>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección Progreso Académico -->
            <h3 class="section-title">Progreso académico</h3>

            <div class="metrics-grid">
                <!-- Card Promedio -->
                <div class="metric-card">
                    <span class="metric-label">Promedio general</span>
                    <div class="score-display">
                        <span class="score-number"><?php echo number_format($promedio, 1); ?></span>
                        <span class="score-max">/10</span>
                    </div>
                    <span class="pill-tag green-tag">Buen desempeño</span>
                </div>

                <!-- Card Estado -->
                <div class="metric-card">
                    <span class="metric-label">Estado académico</span>
                    <div class="status-display">
                        <div class="check-circle">✓</div>
                        <div>
                            <strong>Al día</strong>
                            <p>No tienes materias pendientes</p>
                        </div>
                    </div>
                </div>

                <!-- Card Asistencia -->
                <div class="metric-card">
                    <span class="metric-label">Asistencia</span>
                    <div class="attendance-display">
                        <div class="progress-ring" style="--percent: <?php echo $asistencia; ?>%;">
                            <span><?php echo $asistencia; ?>%</span>
                        </div>
                        <div>
                            <strong>Asistencia general</strong>
                            <p>Muy buena asistencia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección Tablas e Hitos -->
            <div class="bottom-grid">
                
                <!-- Tabla Materias del Año Actual -->
                <section class="content-box subjects-section">
                    <h3>Materias del año actual</h3>
                    <table class="subjects-table">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Docente</th>
                                <th>Calificación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materias as $mat): ?>
                            <tr>
                                <td>
                                    <div class="subject-cell">
                                        <span class="subject-icon">📖</span>
                                        <span><?php echo htmlspecialchars($mat['materia']); ?></span>
                                    </div>
                                </td>
                                <td class="teacher-cell"><?php echo htmlspecialchars($mat['docente']); ?></td>
                                <td class="score-cell"><?php echo htmlspecialchars($mat['calificacion']); ?></td>
                                <td><span class="badge-approved"><?php echo htmlspecialchars($mat['estado']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="#" class="link-more">Ver todas las materias</a>
                </section>

                <!-- Próximos hitos académicos -->
                <aside class="content-box milestones-section">
                    <h3>Próximos hitos académicos</h3>
                    
                    <div class="milestones-list">
                        <div class="milestone-item">
                            <span class="milestone-icon">📅</span>
                            <div class="milestone-details">
                                <strong>Entrega de trabajos</strong>
                                <small>Matemática</small>
                            </div>
                            <div class="milestone-date">
                                <strong>30</strong>
                                <span>MAY</span>
                            </div>
                        </div>

                        <div class="milestone-item">
                            <span class="milestone-icon">📅</span>
                            <div class="milestone-details">
                                <strong>Examen parcial</strong>
                                <small>Química</small>
                            </div>
                            <div class="milestone-date">
                                <strong>05</strong>
                                <span>JUN</span>
                            </div>
                        </div>

                        <div class="milestone-item">
                            <span class="milestone-icon">📅</span>
                            <div class="milestone-details">
                                <strong>Entrega de proyectos</strong>
                                <small>Historia</small>
                            </div>
                            <div class="milestone-date">
                                <strong>12</strong>
                                <span>JUN</span>
                            </div>
                        </div>

                        <div class="milestone-item">
                            <span class="milestone-icon">📅</span>
                            <div class="milestone-details">
                                <strong>Evaluación trimestral</strong>
                                <small>Todas las materias</small>
                            </div>
                            <div class="milestone-date">
                                <strong>20</strong>
                                <span>JUN</span>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="link-more flex-between">
                        <span>Ver calendario académico</span>
                        <span>&rsaquo;</span>
                    </a>
                </aside>

            </div>
        </main>
    </div>

</body>
</html>