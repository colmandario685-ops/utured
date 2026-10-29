<?php
session_start();
require_once 'db.php';

// 1. Guardián de sesión: Si no está autenticado, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Prevenir el almacenamiento en caché para que no se pueda retroceder tras cerrar sesión
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$id_estudiante = $_SESSION['usuario_id'];

// Obtener datos del estudiante en sesión
$stmtUser = $pdo->prepare("
    SELECT u.nombre_completo, u.ci, u.correo, 
           g.nivel, g.grupo, 
           r.promedio_general, r.asistencia_porcentaje, r.conducta
    FROM usuarios u
    LEFT JOIN estudiante_grupo eg ON u.id_usuario = eg.id_estudiante
    LEFT JOIN grupos g ON eg.id_grupo = g.id_grupo
    LEFT JOIN resumen_academico r ON u.id_usuario = r.id_estudiante
    WHERE u.id_usuario = ?
");
$stmtUser->execute([$id_estudiante]);
$estudiante = $stmtUser->fetch();

// Obtener materias y notas del estudiante
$stmtMaterias = $pdo->prepare("
    SELECT m.nombre, u.nombre_completo AS docente, m.horario, c.estado, c.promedio
    FROM calificaciones c
    JOIN materias m ON c.id_materia = m.id_materia
    LEFT JOIN usuarios u ON m.docente_id = u.id_usuario
    WHERE c.id_estudiante = ?
");
$stmtMaterias->execute([$id_estudiante]);
$materias = $stmtMaterias->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Portal Estudiante</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="app-container">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <div class="logo">
                <h2>INSTITUCIÓN EDUCATIVA</h2>
            </div>
            <nav class="menu">
                <a href="index.php" class="active">Área de Estudiantes</a>
                <a href="perfil.php">Mi perfil</a>
                <a href="#">Nivel académico</a>
                <a href="nivel_academico.php">Mis datos académicos</a>
                <a href="#">Mis materias</a>
                
                <!-- Enlace de Cerrar Sesión dirigiéndose al controlador -->
                <a href="auth_controller.php?action=logout" class="logout">Cerrar sesión</a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <header class="topbar">
              
            <nav>

            <div class="nav-links">
                     <a href="index.php">Inicio</a>
            <a href="eventos.php">Eventos</a>
            <a href="noticias.php">Noticias</a>
            <a href="horarios.php">Horarios</a>
            <a href="ayuda.php">Ayuda</a>
                </div>
                </nav>
                <div class="user-profile">
                    <span>Estudiante: <strong><?php echo htmlspecialchars($estudiante['nombre_completo'] ?? $_SESSION['nombre']); ?></strong></span>
                </div>
            </header>

            <section class="dashboard">
                <h1>Mis datos académicos</h1>

                <!-- Tarjetas de resumen -->
                <div class="cards-grid">
                    <div class="card">
                        <h3>Promedio general</h3>
                        <p class="metric"><?php echo htmlspecialchars($estudiante['promedio_general'] ?? '0.0'); ?> <small>/10</small></p>
                        <span class="badge success">Buen desempeño</span>
                    </div>
                    <div class="card">
                        <h3>Asistencia general</h3>
                        <p class="metric"><?php echo htmlspecialchars($estudiante['asistencia_porcentaje'] ?? '0'); ?>%</p>
                        <span class="badge info">Muy buena asistencia</span>
                    </div>
                    <div class="card">
                        <h3>Nivel actual</h3>
                        <p class="highlight"><?php echo htmlspecialchars($estudiante['nivel'] ?? 'Sin asignar'); ?></p>
                        <small>Grupo <?php echo htmlspecialchars($estudiante['grupo'] ?? '-'); ?></small>
                    </div>
                </div>

                <!-- Tabla de materias -->
                <div class="table-container">
                    <h2>Materias del año actual</h2>
                    <?php if (!empty($materias)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Docente</th>
                                <th>Horario</th>
                                <th>Estado</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materias as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($m['docente'] ?? 'Sin asignar'); ?></td>
                                <td><?php echo htmlspecialchars($m['horario'] ?? '-'); ?></td>
                                <td><span class="status <?php echo strtolower(str_replace(' ', '-', $m['estado'])); ?>"><?php echo htmlspecialchars($m['estado']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($m['promedio']); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p>No tienes materias registradas o asignadas aún.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>