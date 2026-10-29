<?php
session_start();
require_once 'db.php';

// Verificación de sesión de estudiante
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Control de caché para mayor seguridad
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$id_estudiante = $_SESSION['usuario_id'];

// Obtención de datos del usuario autenticado
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
    <title>UTUred - Noticias</title>
    <link rel="stylesheet" href="noticias.css">
</head>
<body>

    <!-- Header superior de navegación -->
    <header class="topbar">
        <div class="logo-section">
            <div class="logo-icon">UTU</div>
            <div class="logo-text">
                <span class="brand-title">INSTITUCIÓN EDUCATIVA UTU</span>
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
            <span class="user-name">Estudiante: <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></span>
        </div>
    </header>

    <!-- Banner principal con ilustración -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Noticias</h1>
            <p>Enterate de las últimas novedades, comunicados<br>y actividades de nuestra institución UTU.</p>
        </div>
        <div class="hero-illustration">
            <div class="illustration-card">
                <div class="card-header">NOTICIAS</div>
                <div class="card-body">
                    <div class="img-placeholder"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line short"></div>
                </div>
            </div>
            <div class="illustration-decor">
                <span class="plant">🪴</span>
                <span class="books">📚</span>
            </div>
        </div>
    </section>

    <!-- Contenido principal de Noticias -->
    <main class="news-container">
        
        <!-- Sección: Noticias Destacadas -->
        <section class="featured-section">
            <h2 class="section-title">⭐ Noticias destacadas</h2>
            <div class="featured-grid">
                
                <!-- Card 1 -->
                <article class="news-card">
                    <div class="card-img-wrapper">
                        <img src="../utured/fotos/int.jfif" alt="Presentacion de proyecto">
                        <span class="badge badge-institucional">INSTITUCIONAL</span>
                    </div>
                    <div class="card-body">
                        <span class="card-date">📅 17/18 de diciembre de 2026</span>
                        <h3>Presentacion de proyecto</h3>
                        <p> Se realizan presentaciones en el cine, es obligatorio asistir. Los esperamos a todos</p>
                        
                    
                </article>

                <!-- Card 2 -->
                <article class="news-card">
                    <div class="card-img-wrapper">
                        <img src="../utured/fotos/def.jfif" alt="Defensa de proyecto">
                        <span class="badge badge-talleres">TALLERES</span>
                    </div>
                    <div class="card-body">
                        <span class="card-date">📅 24/25 de noviembre de 2026</span>
                        <h3>Defensa de proyecto</h3>
                        <p>Alumnos de 3bt defienden proyecto anual</p>
                    
                </article>

                <!-- Card 3 -->
                <article class="news-card">
                    <div class="card-img-wrapper">
                        <img src="../utured/fotos/acto.jfif" alt="Acto fin de curso">
                        <span class="badge badge-estudiantes">ESTUDIANTES</span>
                    </div>
                    <div class="card-body">
                        <span class="card-date">📅 21 de diciembre de 2026</span>
                        <h3>Acto fin de curso</h3>
                        <p>Se realiza acto de fin de curso, los esperamos a todos</p>
                    
                </article>

            </div>
        </section>

        <!-- Sección: Más Noticias (Lista Horizontal) -->
        <section class="more-news-section">
            <h2 class="section-title">Más noticias</h2>
            <div class="list-news">
                
                <!-- Item 1 -->
                <div class="news-list-item">
                    <img src="../utured/fotos/jgg.jpg" alt="Fiesta de egresados">
                    <div class="item-content">
                        <div class="item-meta">
                            <span class="card-date">📅 2 de diciembre de 2026</span>
                            <span class="badge badge-comunicados">COMUNICADOS</span>
                        </div>
                        <h4>Fiesta de egresados</h4>
                        <p>Se realizó la fiesta de egresados para alumnnos de 3bt.</p>
                    </div>
                    <div class="item-arrow">&rsaquo;</div>
                </div>

                <!-- Item 2 -->
                <div class="news-list-item">
                    <img src="../utured/fotos/nuv.jpg" alt="Feria educativa">
                    <div class="item-content">
                        <div class="item-meta">
                            <span class="card-date">📅 14 de noviembre de 2026</span>
                            <span class="badge badge-actividades">ACTIVIDADES</span>
                        </div>
                        <h4>Feria educativa</h4>
                        <p>Se realiza feria educativa.</p>
                    </div>
                    <div class="item-arrow">&rsaquo;</div>
                </div>

            </div>

            
        </section>

    </main>

    <!-- Footer de Suscripción -->
    <footer class="bottom-bar">
        
    </footer>

</body>
</html>
