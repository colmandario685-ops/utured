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

// Obtener datos del usuario en sesión
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
    <title>UTUred - Ayuda</title>
    <link rel="stylesheet" href="ayuda.css">
</head>
<body>

    <!-- Header superior de navegación -->
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
            <span class="user-name">Estudiante: <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></span>
        </div>
    </header>

    <!-- Banner Hero de Ayuda -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Ayuda</h1>
            <p>Encuentra respuestas a preguntas frecuentes<br>y ponte en contacto con nosotros si<br>necesitas más ayuda.</p>
        </div>
        <div class="hero-illustration">
            <div class="building-icon">🏫</div>
        </div>
    </section>

    <!-- Contenido Principal -->
    <main class="main-container">
        <div class="help-grid">

            <!-- Columna Izquierda: Preguntas Frecuentes / Acordeones -->
            <section class="faq-section">
                <h2>¿En qué podemos ayudarte?</h2>

                <div class="accordion-list">
                    
                    <!-- Opción 1: cuenta y acceso -->
                    <details class="accordion-item">
                        <summary>
                            <div class="item-title">
                                <span class="icon icon-purple">👤</span>
                                <div>
                                    <strong>Cuenta y acceso</strong>
                                    <p>Problemas para iniciar sesión, recuperar contraseña o gestionar tu cuenta.</p>
                                </div>
                            </div>
                            <span class="chevron">&rsaquo;</span>
                        </summary>
                        <div class="accordion-content">
                            <p>Si olvidaste tu contraseña, puedes restablecerla desde la pantalla de inicio de sesión seleccionando la opción "¿Olvidaste tu contraseña?". También puedes contactar la ayuda de la pagina o soporte técnico para gestionar el acceso a tu cuenta institucional.</p>
                        </div>
                    </details>

                    <!-- Opción 2: Plataforma y cursos -->
                    <details class="accordion-item">
                        <summary>
                            <div class="item-title">
                                <span class="icon icon-green">📖</span>
                                <div>
                                    <strong>Plataforma y cursos</strong>
                                    <p>Dudas sobre el uso de la plataforma, cursos, tareas y calificaciones.</p>
                                </div>
                            </div>
                            <span class="chevron">&rsaquo;</span>
                        </summary>
                        <div class="accordion-content">
                            <p>Consulta la pestaña "Inicio" o "Mis materias" para verificar tus horarios, lista de docentes asignados e historial de calificaciones actualizado en tiempo real.</p>
                        </div>
                    </details>

                    <!-- Opción 3: Eventos y actividades -->
                    <details class="accordion-item">
                        <summary>
                            <div class="item-title">
                                <span class="icon icon-yellow">📅</span>
                                <div>
                                    <strong>Eventos y actividades</strong>
                                    <p>Información sobre eventos, inscripciones y actividades de la institución.</p>
                                </div>
                            </div>
                            <span class="chevron">&rsaquo;</span>
                        </summary>
                        <div class="accordion-content">
                            <p>Revisa la sección de "Noticias" para enterarte de los talleres, jornadas de integración, ferias de ciencias y actos institucionales organizados por la UTU.</p>
                        </div>
                    </details>

                    <!-- Opción 4: Otras consultas -->
                    <details class="accordion-item">
                        <summary>
                            <div class="item-title">
                                <span class="icon icon-blue">❓</span>
                                <div>
                                    <strong>Otras consultas</strong>
                                    <p>Otras preguntas frecuentes y contacto con soporte técnico.</p>
                                </div>
                            </div>
                            <span class="chevron">&rsaquo;</span>
                        </summary>
                        <div class="accordion-content">
                            <p>Para consultas administrativas adicionales, te invitamos a comunicarte directamente a través de cualquiera de nuestros canales oficiales de red social o WhatsApp.</p>
                        </div>
                    </details>

                </div>
            </section>

            <!-- Columna Derecha: Redes Sociales -->
            <aside class="social-section">
                <h2>Síguenos en nuestras redes</h2>

                <div class="social-cards">
                    
                    <!-- Instagram -->
                    <a href="https://instagram.com" target="_blank" class="social-card">
                        <div class="social-info">
                            <span class="social-icon icon-instagram">📷</span>
                            <div>
                                <strong>Instagram</strong>
                                <p>@utu_guichon</p>
                            </div>
                        </div>
                        <span class="chevron">&rsaquo;</span>
                    </a>

                    <!-- Facebook -->
                    <a href="https://facebook.com" target="_blank" class="social-card">
                        <div class="social-info">
                            <span class="social-icon icon-facebook">📘</span>
                            <div>
                                <strong>Facebook</strong>
                                <p>UTU Guichón</p>
                            </div>
                        </div>
                        <span class="chevron">&rsaquo;</span>
                    </a>

                    <!-- WhatsApp -->
                    <a href="https://wa.me/59899123456" target="_blank" class="social-card">
                        <div class="social-info">
                            <span class="social-icon icon-whatsapp">💬</span>
                            <div>
                                <strong>WhatsApp</strong>
                                <p>098 286 051</p>
                            </div>
                        </div>
                        <span class="chevron">&rsaquo;</span>
                    </a>

                </div>
            </aside>

        </div>
    </main>

</body>
</html>