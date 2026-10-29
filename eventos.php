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

// Obtener información del usuario en sesión
$stmtUser = $pdo->prepare("SELECT nombre_completo FROM usuarios WHERE id_usuario = ?");
$stmtUser->execute([$id_estudiante]);
$estudiante = $stmtUser->fetch();
$nombre_usuario = $estudiante['nombre_completo'] ?? $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Eventos Institucionales</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="eventos.css">
</head>
<body>

    <!-- Header / Topbar Global -->
    <header class="topbar">
        <div class="logo-section">
            <div class="logo-icon">UTU</div>
            <div class="logo-text">
                <span class="brand-title">INSTITUCIÓN EDUCATIVA</span>
                <span class="brand-sub">Formamos para el futuro</span>
            </div>
        </div>
        <nav>
            <ul class="nav-links">
                 <a href="index.php">Inicio</a>
            <a href="eventos.php">Eventos</a>
            <a href="noticias.php">Noticias</a>
            <a href="horarios.php">Horarios</a>
            <a href="ayuda.php">Ayuda</a>
            </ul>
        </nav>
        <div class="user-dropdown">
            <span>Estudiante: <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></span>
        </div>
    </header>

    <!-- Banner Hero de Eventos -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Eventos Institucionales</h1>
            <p>Descubre las próximas actividades, ferias, talleres y actos que<br>se realizarán en nuestra comunidad educativa.</p>
        </div>
        <div class="hero-illustration">
            <span class="event-hero-icon">🎉</span>
        </div>
    </section>

    <!-- Contenido Principal -->
    <main class="main-container">

        <!-- Barra de Filtros / Categorías -->
        <div class="events-filter-bar">
            <button class="filter-btn active">Todos los eventos</button>
            <button class="filter-btn">Académicos</button>
            <button class="filter-btn">Culturales y Deportivos</button>
            <button class="filter-btn">Talleres y Ferias</button>
        </div>

        <!-- Grid de Tarjetas de Eventos -->
        <div class="events-grid">

            <!-- Evento 1 -->
            <article class="card event-card">
                <div class="event-image-wrapper">
                    <img src="fotos/acto.jfif" alt="Acto fin de curso" class="event-img" onerror="this.src='https://via.placeholder.com/400x200?text=Feria+de+Ciencias'">
                    <span class="event-badge badge-blue">Académico</span>
                </div>
                <div class="event-body">
                    <div class="event-date">
                        <span class="date-icon">📅</span> 13 de Noviembre de 2026
                    </div>
                    <h3 class="event-title">Feria educativa</h3>
                    <p class="event-desc">Se realiza feria educativa en UTU GUICHÓN.</p>
                    <div class="event-footer">
                        <span class="event-location"><a href="https://maps.app.goo.gl/FjyuGbeDi4Ppscoa7">📍 UTU GUICHÓN</span> </a>
                        <a href="#" class="btn-primary btn-sm">Ver detalles</a>
                    </div>
                </div>
            </article>

            <!-- Evento 2 -->
            <article class="card event-card">
                <div class="event-image-wrapper">
                    <img src="fotos/acto.jfif" alt="Acto de Fin de Cursos" class="event-img" onerror="this.src='https://via.placeholder.com/400x200?text=Acto+de+Fin+de+Cursos'">
                    <span class="event-badge badge-purple">Institucional</span>
                </div>
                <div class="event-body">
                    <div class="event-date">
                        <span class="date-icon">📅</span> 21 de Diciembre de 2026
                    </div>
                    <h3 class="event-title">Acto de Fin de Cursos</h3>
                    <p class="event-desc">Se realiza acto de fin de cursos para estudiantes de la Escuela Tecnica de Guichón.</p>
                    <div class="event-footer">
                        <span class="event-location">📍 Cine de Guichón</span>
                        <a href="#" class="btn-primary btn-sm">Ver detalles</a>
                    </div>
                </div>
            </article>

            <!-- Evento 3 -->
            <article class="card event-card">
                <div class="event-image-wrapper">
                    <img src="fotos/acto.jfif" alt="Jornada Deportiva" class="event-img" onerror="this.src='https://via.placeholder.com/400x200?text=Jornada+Deportiva'">
                    <span class="event-badge badge-green">Deportes</span>
                </div>
                <div class="event-body">
                    <div class="event-date">
                        <span class="date-icon">📅</span> 5-6 de noviembre de 2026
                    </div>
                    <h3 class="event-title">Campamento de recreacion</h3>
                    <p class="event-desc">9no van de campamento a la naturaleza.</p>
                    <div class="event-footer">
                        <span class="event-location">📍 Naturaleza</span>
                        <a href="#" class="btn-primary btn-sm">Ver detalles</a>
                    </div>
                </div>
            </article>

        </div>
    </main>

</body>
</html>