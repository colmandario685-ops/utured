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


$id_estudiante =$_SESSION['usuario_id'];


// Obtener información del usuario en sesión
$stmtUser =$pdo->prepare("SELECT nombre_completo FROM usuarios WHERE id_usuario = ?");
$stmtUser->execute([$id_estudiante]);$estudiante = $stmtUser->fetch();$nombre_usuario = $estudiante['nombre_completo'] ?? $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Horarios</title>
    <link rel="stylesheet" href="horarios.css?v=<?php echo @filemtime(__DIR__ . '/horarios.css'); ?>">
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
            <a href="horarios.php" class="active">Horarios</a>
            <a href="ayuda.php">Ayuda</a>
        </nav>
        <div class="user-dropdown">
            <span class="user-icon">👤</span>
            <span class="user-name">Estudiante: <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></span>
        </div>
    </header>


    <!-- Banner Hero de Horarios -->
    <section class="hero-banner">
        <div class="hero-content">
            <div class="hero-title-group">
                <span class="clock-icon">🕒</span>
                <h1>Horarios</h1>
            </div>
            <h2>Organiza tu tiempo y mantente al día con tus clases.</h2>
            <p>Aquí puedes consultar los horarios de clases, atención y otras actividades de la institución.</p>
        </div>
        <div class="hero-illustration">
            <div class="illustration-calendar">
                <div class="cal-top"><span></span><span></span><span></span></div>
                <div class="cal-grid">
                    <div class="cal-cell"></div>
                    <div class="cal-cell"></div>
                    <div class="cal-cell active"></div>
                    <div class="cal-cell"></div>
                </div>
                <div class="cal-clock">⏰</div>
            </div>
            <div class="illustration-decor">
                <span class="decor-books">📚</span>
                <span class="decor-plant">🪴</span>
            </div>
        </div>
    </section>


    <!-- Contenido Principal -->
    <main class="main-container">


        <!-- Pestañas principales -->
        <div class="tabs-header">
            <button type="button" class="tab-btn active" data-tab="horarios">📅 Horarios de clases</button>
            <button type="button" class="tab-btn" data-tab="atencion">👥 Atención al público</button>
            <button type="button" class="tab-btn" data-tab="actividades">⭐ Otras actividades</button>
        </div>


        <!-- PESTAÑA 1: HORARIOS DE CLASES -->
        <div id="tab-horarios" class="tab-pane active" style="display:block;">
        <div class="content-grid">
           
            <!-- Panel Izquierdo: Horario de Clases -->
            <section class="schedule-section">
                <div class="section-header">
                    <span class="icon-calendar">📅</span>
                    <div>
                        <h3>Horarios de clases</h3>
                        <p>Consulta los horarios correspondientes a tu curso u orientación.</p>
                    </div>
                </div>


                <!-- Filtro de Cursos -->
                <form class="filter-bar" id="filterForm">
                    <div class="filter-group">
                        <label>Grupo / Curso</label>
                        <select name="curso" id="selectCurso">
                            <optgroup label="Educación Básica">
                                <option value="7A">7mo A</option>
                                <option value="7B">7mo B</option>
                                <option value="7C">7mo C</option>
                                <option value="8A">8vo A</option>
                                <option value="8B">8vo B</option>
                                <option value="9A">9no A</option>
                                <option value="9B">9no B</option>
                                <option value="9C">9no C</option>
                            </optgroup>
                            <optgroup label="Informática (MD)">
                                <option value="1MD_INFO">1ro MD Informática</option>
                                <option value="2MD_INFO">2do MD Informática</option>
                                <option value="3MD_INFO">3ro MD Informática</option>
                            </optgroup>
                            <optgroup label="Deporte (MC)">
                                <option value="1MC_DEP">1ro MC Deporte</option>
                                <option value="2MC_DEP">2do MC Deporte</option>
                                <option value="3MC_DEP">3ro MC Deporte</option>
                            </optgroup>
                            <optgroup label="Administración (MA)">
                                <option value="1MA_ADM">1ro MA Administración</option>
                                <option value="2MA_ADM">2do MA Administración</option>
                                <option value="3MA_ADM">3ro MA Administración</option>
                            </optgroup>
                            <optgroup label="Agrario (MB)">
                                <option value="1MB_AGR">1ro MB Agrario</option>
                                <option value="2MB_AGR">2do MB Agrario</option>
                                <option value="3MB_AGR">3ro MB Agrario</option>
                            </optgroup>
                            <optgroup label="Gastronomía (DA)">
                                <option value="1DA_GAS">1ro DA Gastronomía</option>
                                <option value="2DA_GAS">2do DA Gastronomía</option>
                                <option value="3DA_GAS">3ro DA Gastronomía</option>
                            </optgroup>
                        </select>
                    </div>
                    <button type="submit" class="btn-filter">Filtrar</button>
                </form>


                <!-- Cuadrícula con Turnos Mañana y Tarde -->
                <div class="schedule-tables-wrapper">
                   
                    <!-- TURNO MAÑANA -->
                    <div class="shift-block">
                        <h4 class="shift-title">Turno Mañana</h4>
                        <div class="table-responsive">
                            <table class="schedule-table" id="tablaManana">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Hora</th>
                                        <th>LUNES</th>
                                        <th>MARTES</th>
                                        <th>MIÉRCOLES</th>
                                        <th>JUEVES</th>
                                        <th>VIERNES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-mod="1°">
                                        <td class="num-col">1°</td><td class="time-col">07:15 - 08:00</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="2°">
                                        <td class="num-col">2°</td><td class="time-col">08:00 - 08:45</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="3°">
                                        <td class="num-col">3°</td><td class="time-col">08:45 - 09:30</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="4°">
                                        <td class="num-col">4°</td><td class="time-col">09:30 - 10:15</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr class="recreo-row">
                                        <td colspan="7">RECREO 10'</td>
                                    </tr>
                                    <tr data-mod="5°">
                                        <td class="num-col">5°</td><td class="time-col">10:25 - 11:10</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="6°">
                                        <td class="num-col">6°</td><td class="time-col">11:10 - 11:55</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="7°">
                                        <td class="num-col">7°</td><td class="time-col">11:55 - 12:40</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="8°">
                                        <td class="num-col">8°</td><td class="time-col">12:40 - 13:25</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- TURNO TARDE -->
                    <div class="shift-block">
                        <h4 class="shift-title">Turno Tarde</h4>
                        <div class="table-responsive">
                            <table class="schedule-table" id="tablaTarde">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Hora</th>
                                        <th>LUNES</th>
                                        <th>MARTES</th>
                                        <th>MIÉRCOLES</th>
                                        <th>JUEVES</th>
                                        <th>VIERNES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-mod="-">
                                        <td class="num-col">-</td><td class="time-col">12:45 - 13:30</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="1°">
                                        <td class="num-col">1°</td><td class="time-col">13:30 - 14:15</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="2°">
                                        <td class="num-col">2°</td><td class="time-col">14:15 - 15:00</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="3°">
                                        <td class="num-col">3°</td><td class="time-col">15:00 - 15:45</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="4°">
                                        <td class="num-col">4°</td><td class="time-col">15:45 - 16:30</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr class="recreo-row">
                                        <td colspan="7">RECREO 10'</td>
                                    </tr>
                                    <tr data-mod="5°">
                                        <td class="num-col">5°</td><td class="time-col">16:40 - 17:25</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="6°">
                                        <td class="num-col">6°</td><td class="time-col">17:25 - 18:10</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="7°">
                                        <td class="num-col">7°</td><td class="time-col">18:10 - 18:55</td>
                                        <td data-dia="LUNES"></td><td data-dia="MARTES"></td><td data-dia="MIERCOLES"></td><td data-dia="JUEVES"></td><td data-dia="VIERNES"></td>
                                    </tr>
                                    <tr data-mod="8°">
                                        <td class="num-col">8°</td><td class="time-col">18:55 - 19:40</td>
                                        <td class="disabled-cell"></td>
                                        <td class="disabled-cell"></td>
                                        <td class="disabled-cell"></td>
                                        <td class="disabled-cell"></td>
                                        <td data-dia="VIERNES"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>


                <p class="schedule-note">ℹ️ Los horarios están sujetos a cambios. Consulta regularmente para estar al día.</p>
            </section>


            <!-- Panel Derecho: Información Secundaria -->
            <aside class="sidebar-info">
                <div class="info-card">
                    <div class="card-title">
                        <span class="icon">👥</span>
                        <h4>Atención al público</h4>
                    </div>
                    <ul class="hours-list">
                        <li><span>Lunes a Viernes</span> <strong>7:00 a.m. - 1:00 p.m.</strong></li>
                        <li><span>Sábado</span> <strong>8:00 a.m. - 12:00 m.</strong></li>
                    </ul>
                    <p class="info-text">ℹ️ Si tenes dudas sobre horarios,noticias,etc. Se puede cosultar en el apartado de Ayuda.</p>
                </div>


                <div class="info-card">
                    <div class="card-title">
                        <span class="icon">⭐</span>
                        <h4>Otras actividades</h4>
                    </div>
                    <ul class="activities-list">
                        <li><a href="#">Actividades extracurriculares <span>&rsaquo;</span></a></li>
                        <li><a href="#">Reuniones de padres <span>&rsaquo;</span></a></li>
                        <li><a href="#">Capacitaciones y talleres <span>&rsaquo;</span></a></li>
                    </ul>
                    <a href="#" class="link-calendar">Ver calendario completo</a>
                </div>
            </aside>


        </div>
        </div> <!-- /#tab-horarios -->

        <!-- ========================================== -->
        <!-- PESTAÑA 2: ATENCIÓN AL PÚBLICO             -->
        <!-- ========================================== -->
        <div id="tab-atencion" class="tab-pane" style="display:none;">
            <div class="atencion-container">

                <!-- Datos de la escuela, teléfonos y correos -->
                <div class="info-escuela-card">
                    <h3>Escuela Técnica de Guichón (UTU)</h3>
                    <p><strong>📍 Dirección:</strong> Luis A. De Herrera N°419, Guichón, Departamento de Paysandú, Uruguay</p>

                    <div class="horarios-detalle">
                        <h4>📞 Teléfonos de contacto</h4>
                        <ul class="contacto-lista">
                            <li><strong>Secretaría / Administración:</strong> <a href="tel:+099669960">099669960</a></li>
                            <li><strong>Dirección:</strong> <a href="tel:+59847420001">59847420001</a></li>
                            <li><strong>Adscripción / Bedelía:</strong> <a href="tel:+47422016">47422016</a></li>
                            <li><strong>WhatsApp institucional:</strong> <a href="tel:+47422016">47422016</a></li>
                        </ul>
                    </div>

                    <div class="horarios-detalle">
                        <h4>✉️ Correos electrónicos</h4>
                        <ul class="contacto-lista">
                            <li><strong>Consultas generales:</strong> <a href="mailto:contacto@utuguichon.edu.uy">escuelatecnicadeguichon@gmail.com</a></li>
                            <li><strong>Secretaría:</strong> <a href="mailto:secretaria@utuguichon.edu.uy">escuelatecnicadeguichon@gmail.com</a></li>
                            <li><strong>Dirección:</strong> <a href="mailto:direccion@utuguichon.edu.uy">escuelatecnicadeguichon@gmail.com</a></li>
                            <li><strong>Adscripción:</strong> <a href="mailto:adscripcion@utuguichon.edu.uy">099669960</a></li>
                        </ul>
                    </div>

                    <div class="horarios-detalle">
                        <h4>🕒 Horarios de atención administrativa</h4>
                        <ul>
                            <li><strong>Lunes a Viernes:</strong> 07:00 a.m. - 12:00 p.m.</li>
                            <li><strong>Lunes a Viernes:</strong> 12:00 p.m. - 16:00 p.m</li>
                            <li><strong>Lunes a Viernes:</strong> 16:00 p.m. - 22:00 p.m</li>
                        </ul>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="map-card">
                    <h3>Ubicación geográfica</h3>
                    <div class="map-responsive">
                        <iframe
                            title="Mapa de la Escuela Técnica de Guichón"
                            src="https://maps.google.com/maps?q=Escuela+T%C3%A9cnica+Guich%C3%B3n+UTU,+Guich%C3%B3n,+Paysand%C3%BA,+Uruguay&hl=es&z=16&output=embed"
                            width="100%"
                            height="320"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
<!-- Alertas de estado del formulario -->
<?php if (isset($_GET['enviado']) && $_GET['enviado'] == '1'): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; margin: 20px auto; max-width: 1100px; border-radius: 6px; border: 1px solid #c3e6cb;">
        ✅ ¡Tu mensaje se ha enviado correctamente a la institución! Nos pondremos en contacto pronto.
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px 20px; margin: 20px auto; max-width: 1100px; border-radius: 6px; border: 1px solid #f5c6cb;">
        ❌ Hubo un error al enviar el mensaje. Por favor, verifica que todos los campos estén completos correctamente.
    </div>
<?php endif; ?>
                <!-- Formulario de contacto -->
                <div class="contacto-card">
                    <h3>Formulario de contacto</h3>
                    <form id="contactForm" action="enviar_contacto.php" method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre completo</label>
                            <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre...">
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email" required placeholder="tu.correo@ejemplo.com">
                        </div>
                        <div class="form-group">
                            <label for="asunto">Asunto</label>
                            <input type="text" id="asunto" name="asunto" required placeholder="Motivo de la consulta...">
                        </div>
                        <div class="form-group">
                            <label for="mensaje">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="4" required placeholder="Escribe tu mensaje aquí..."></textarea>
                        </div>
                        <button type="submit" class="btn-filter">Enviar mensaje</button>
                    </form>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- PESTAÑA 3: OTRAS ACTIVIDADES (ejemplos)    -->
        <!-- ========================================== -->
        <div id="tab-actividades" class="tab-pane" style="display:none;">
            <div class="section-header">
                <span class="icon-calendar">⭐</span>
                <div>
                    <h3>Otras actividades</h3>
                    <p>Eventos, talleres y reuniones de la institución. (Datos de ejemplo, modifícalos a gusto.)</p>
                </div>
            </div>

            <div class="actividades-grid">
                <article class="actividad-card">
                    <span class="actividad-tag">Taller</span>
                    <h4> Viaje de musica</h4>
                    <p>Se realiza viaje educativo a Paysandú.</p>
                    <ul>
                        <li>📅 Viernes 23 de octubre</li>
                        <li>🕒 14:00 - 18:30 hs</li>
                        <li>📍 Paysandú</li>
                    </ul>
                </article>

                <article class="actividad-card">
                    <span class="actividad-tag">Deporte</span>
                    <h4> Club de ciencia</h4>
                    <p>Se realiza el club de ciencia en Maldonado.</p>
                    <ul>
                        <li>📅 Martes 11 de noviembre</li>
                        <li>🕒 12:00 - 17:00 hs</li>
                        <li>📍 Maldonado</li>
                    </ul>
                </article>

                <article class="actividad-card">
                    <span class="actividad-tag">Reunión</span>
                    <h4> Muestra anual</h4>
                    <p>Se realiza Muestra anual.</p>
                    <ul>
                        <li>📅 Viernes 13 de noviembre</li>
                        <li>🕒 08:00 - 09:00 am hs</li>
                        <li>📍 Escuela Técnica De Guichón</li>
                    </ul>
                </article>

                <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Presentaciones en el Cine</h4>
                    <p>Se realizan presentaciones de alumnos de bachillerato.</p>
                    <ul>
                        <li>📅 18/19 de noviembre</li>
                        <li>🕒 13:00 - 18:00 hs</li>
                        <li>📍 Cine municipal de Guichón</li>
                    </ul>
                </article>

                <article class="actividad-card">
                    <span class="actividad-tag">Taller</span>
                    <h4> Fin de curso</h4>
                    <p>Se realiza Acto de fin de curso.</p>
                    <ul>
                        <li>📅 Viernes 20 de noviembre</li>
                        <li>🕒 13:00 - 18:30 hs</li>
                        <li>📍 Escuela Tecnica de Guichón</li>
                    </ul>
                </article>

                <article class="actividad-card">
                    <span class="actividad-tag">Gastronomía</span>
                    <h4> Defensa de proyecyo</h4>
                    <p>Alumnos de Tercero de BT.</p>
                    <ul>
                        <li>📅 24/25  de noviembre</li>
                        <li>🕒 13:00 - 18:00 hs</li>
                        <li>📍 Escuela Tecnica de Guichon</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Presentaciones en el Cine</h4>
                    <p>Se realizan presentaciones de alumnos de bachillerato.</p>
                    <ul>
                        <li>📅 18/19 de noviembre</li>
                        <li>🕒 13:00 - 18:00 hs</li>
                        <li>📍 Cine municipal de Guichón</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Campamento CTT</h4>
                    <p>Alumnos de CTT realizan campamento.</p>
                    <ul>
                        <li>📅 18/19 de noviembre</li>
                        <li>🕒 Todo el Dia</li>
                        <li>📍 Naturaleza</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Fin EBI BACH</h4>
                    <p> Llega a su fin EBI BACH.</p>
                    <ul>
                        <li>📅 27 de noviembre</li>
                        <li>🕒 Todo el dia</li>
                        <li>📍 Escuela Tecnica de Guichón</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Viaje 9no Paysandú</h4>
                    <p>Viaje de 9no a Paysandú.</p>
                    <ul>
                        <li>📅 27  de noviembre</li>
                        <li>🕒 Todo el Dia</li>
                        <li>📍 Paysandú</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Olimpiada Robotica</h4>
                    <p>Se realiza olimpiada de robotica.</p>
                    <ul>
                        <li>📅 11 de noviembre</li>
                        <li>🕒 Todo el dia</li>
                        <li>📍 Montevideo</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Oferta Educatica</h4>
                    <p>Se realiza oferta educativa.</p>
                    <ul>
                        <li>📅 17 de noviembre</li>
                        <li>🕒 Todo el dia</li>
                        <li>📍 Escuela Tecnica de Guichón</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Termas de Guaviyú</h4>
                    <p>Alumnos de 2do de Administracion realiza viaje a termas.</p>
                    <ul>
                        <li>📅 17/18 de noviembre</li>
                        <li>🕒 Todo el dia</li>
                        <li>📍 Termas de Guaviyú</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Viaje Colonia</h4>
                    <p>Alumnos realizan Viaje a colonia.</p>
                    <ul>
                        <li>📅 4 de noviembre</li>
                        <li>🕒 Todo el dia</li>
                        <li>📍 Colonia</li>
                    </ul>
                </article>
                  <article class="actividad-card">
                    <span class="actividad-tag">Feria</span>
                    <h4> Queguay </h4>
                    <p>Alumnos de bachillerato realizan viaje al Queguay.</p>
                    <ul>
                        <li>📅 3 de noviembre</li>
                        <li>🕒 Todo el dia</li>
                        <li>📍 Queguay</li>
                   
                    </ul>
                </article>

            </div>
        </div>

    </main>


    <!-- Footer Inferior -->
    <footer class="bottom-bar">
        <p>ℹ️ ¿Tienes dudas sobre los horarios? Visita la sección de Ayuda o comunícate con la institución.</p>
    </footer>


    <!-- Script JavaScript para rellenar  -->
    <script>
        const horariosPorCurso = {
            '7A': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'TEC' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'TEC / LENG' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'LENG ESP' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'CFQ' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'HISTOR' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'MAT' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'LENG ESP' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'LENG ESP' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'TEC/MAT' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'TEC' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'TEC / ING' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'INGLES' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'BIOLOG' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'ED. FISIC' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'GEOGRAF' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'GEOGRAF' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'HISTOR' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'HISTOR' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'INFORMATIC' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'INFORMATIC' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'CFQ' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: 'SEX' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'SEX' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'BIOLOG' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'BIOLOG' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'ARTE' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'INGLES' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'INGLES' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'ED. FISIC' },


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'GEOGRAF' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'GEOGRAF' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'ARTE' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'ARTE' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'TO' }
            ],
            '7B': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'CFQ' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'LENG' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'TEC/LENG' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'TEC' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'TEC' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'HISTOR' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'ED. FISICA' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'ED. FISICA' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'TEC/MAT' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'TEC/INGL' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'INGLES' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'CFQ' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'BIOLOG' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'BIOLOG' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'INF' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'INF' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'HISTOR' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'HISTOR' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'GEOGRAF' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'GEOGRAF' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'LENG' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'LENG' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'MAT' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'BIOLOG' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'ARTE' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'INGLES' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'INGLES' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'MAT' },


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'SEX' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'SEX' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'GEOGRAF' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'GEOGRAF' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'ARTE' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'ARTE' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'TO' },
            ],
             '7C': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '.SEX' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'SEX' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'CFQ' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'CFQ' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'TEC/MAT' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'TEC' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'BIO' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'GEO' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'GEO' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'BIO' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'BIO' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'LENGUA' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'TEC' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'TEC/ING' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'TEC/LENGUA' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'LENGUA' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'LENGUA' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'ARTE' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'ARTE' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'HIST' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'HIST' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'EDU.F' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'EDU.F' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'GEO' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'GEO' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'INGLES' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'ARTE' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'HIST' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'INFO' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'INFO' },
                { num_modulo: '°', dia_semana: 'JUEVES', materia: 'MAT' },


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '------' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'INGLES' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'INGLES' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'TO' },
            ],
             '8A': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'BIO' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: '----' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'SEX' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'SEX' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'LENGUA' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'LENGUA' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'INFO' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'INFO' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'MAT' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'BIO' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'BIO' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'LENGUA' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'LENGUA' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'ARTE' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'ARTE' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'GEO' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'GEO' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: 'HIST' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'HIST' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'TEC' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'TEC/ING' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'TEC' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'ARTE' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'EDU.F' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: 'EDU.F' },
                
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'TEC/CFQ' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'TEC/MAT' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'GEO' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'CFQ' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'TO' }
            ],

         '8B': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'TEC' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'TEC / LENG' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'LENG ESP' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'CFQ' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'HISTOR' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'MAT' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'LENG ESP' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'LENG ESP' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'TEC/MAT' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'TEC' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'TEC / ING' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'INGLES' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'BIOLOG' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'ED. FISIC' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'GEOGRAF' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'GEOGRAF' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'HISTOR' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'HISTOR' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'INFORMATIC' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'INFORMATIC' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'CFQ' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: 'SEX' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'SEX' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'BIOLOG' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'BIOLOG' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'ARTE' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'INGLES' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'INGLES' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'ED. FISIC' },


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'GEOGRAF' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'GEOGRAF' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'ARTE' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'ARTE' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'TO' }
            ],
            '9A': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'QUIM' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'COMU' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'COMU' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'TEC/FIS' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'TEC/MAT' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'MAT' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'EDU.F' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'EDU.F' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'TEC' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'TEC' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'INFO' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'INFO' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'FORM.C' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'FORM.C' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'MAT' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'MAT' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'COMU' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'COMU' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'HIST' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'A Y L' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'BIO' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'A Y L' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'A Y L' }
            ],
            '9B': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'QUIM' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'QUIM' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'INFO' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'INFO' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'COMU' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'COMU' },
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'ING' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'TEC/MAT' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'TEC/FIS' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'FORM.C' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'FORM.C' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'A Y L' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'A Y L' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'COMU' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'COMU' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'HIST' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'TEC' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'TEC' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: 'A Y L' },


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'EDU.F' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'EDU.F' }
            ],
             '9C': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'BIO' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'COMU' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'COMU' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'INFO' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'INFO' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'EDU.F' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'EDU.F' },
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'FIS' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'BIO' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'BIO' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'TEC/MAT' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'TEC' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'ING' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'FORM.C' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'FORM.C' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'A Y L' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'A Y L' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'COMU' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'HIST' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'A Y L' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'TEC/FIS' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'TEC' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'COMU' }
            ],
            '1MD_INFO': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'PROG' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'PROG' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'FIS' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'FIS' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'LOG' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'LOG' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'ING' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'BIO' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'CIU' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'CIU' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'LENG' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'LENG' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'LOG' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'ING' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'ELEC' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'ELEC' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'BIO' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'SOPO' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: '' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'LENG' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'LENG' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'SOPO' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'HIS' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'HIS' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'SOPO' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'SOPO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'TO' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: 'TO' }
            ],
            '2MD_INFO': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'BAS.D' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'BAS.D' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'HIS' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'HIS' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'MAT CTS' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'MAT CTS' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'MAT' },
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'REDES' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'LEG' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'LEG' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'PROG' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'PROG' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'REDES' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'REDES' },
                


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'SIST.OP' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'PROG' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'PROG' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'APT' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'APT' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'BAS.D' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'BAS.D' },
            

                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'MAT CTS' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'SIST.OP' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'SIST.OP' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'SIS.OP' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'T.O' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: 'T.O' }
            ],
             '3MD_INFO': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'FIS' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'MAT CTS' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'SIST.OP' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'SIST.OP' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'SOCI' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'FILO' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'FILO' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'SOCI' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'SOCI' },
               

                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'PROG' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'PROG' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'PROG' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'FIS' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'FIS' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'INGE' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'INGE' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'TO. CIBER' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'MAT CTS' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'MAT CTS' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'FILO' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'INGE' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'INGE' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'EMPR' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'EMPR' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: 'SIST. OP' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'PROG' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'PROG' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'UTULAB' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'UTULAB' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'CALC' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: 'CALC' }
            ],
              '1MC_DEP': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'TEC DIG' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'TEC DIG' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'LEN' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'LEN' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'T.O' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'T.O' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: '----' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: '' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'CIU' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'CIU' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'HIST' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'HIST' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'MAT' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'LEN' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'LEN' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: '----' }
            ],
            '2MC_DEP': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'BIOMEC' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'T.O' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'T.O' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: '----' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'INGE' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: '----' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'BIOMEC' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'BIOMEC' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: '----' },
                


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: '----' },
            

                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'PSI' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'PSI' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'APT' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'APT' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                
            ],
             '3MC_DEP': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: '----' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'FILO' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'EMPR' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'EMPR' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'ERGO' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'PROY' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'PROY' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: '---' },
               

                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'SOCIO' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'SOCIO' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: '----' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'ERGO' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'ERGO' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'FILO' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'FILO' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'SOCIO' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'ING' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: 'ING' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'T.O' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'T.O' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'MAT CTS' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'QUIM' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'QUIM' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'MAT CTS' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'MAT CTS' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: '----' }
            ],

              '1MA_ADM': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'INTRO' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'INTRO' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'BIO' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'LEN' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'LEN' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: '----' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'DERE' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'DERE' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'CIU' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'ORG' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'LEN' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'LEN' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'BIO' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: '----' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'ED.FIN' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'ED.FIN' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'INTRO' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'ORG' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'ORG' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'TEC DIG' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'TEC DIG' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: 'DER' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'DER' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'CIU' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'ING' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'ING' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: '----' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'HIST' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'HIST' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'T.O' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'T.O' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: '----' }
            ],
            '2MA_ADM': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'CONTAB' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'CONTAB' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'T.O' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'T.O' },
                { num_modulo: '10°', dia_semana: 'LUNES', materia: '----' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'LEG' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'LEG' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'MARK' },
                { num_modulo: '9°', dia_semana: 'MARTES', materia: 'MARK' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'CONTAB' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'CONTAB' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'CONTAB' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'DER' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'DOC' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'APT' },
                


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'APT' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'SIST INF' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'SIST INF' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'SIST INF' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'MARK' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'MARK' },
            

                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'DER' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'DER' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'DOC' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'DOC' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                
            ],
             '3MA_ADM': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'CONTB' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'CONTB' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'CONTB' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'COMU' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'COMU' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'EMPR' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'EMPR' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'FILO' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'FILO' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'DIN' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'DIN' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'MAT F' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'MAT F' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'COMU' },
                { num_modulo: '9°', dia_semana: 'MARTES', materia: '----' },
               

                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'ECO' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'ECO' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'DIN' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'MAT CTS' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: '----' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'CONTAB' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'CONTAB' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'CONTAB' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'ECO' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'FILO' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'SOCIO' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: 'SOCIO' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'UTULAB' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'UTULAB' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'COMU' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'COMU' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'EMPR' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: 'EMPR' }
            ],
             '1MB_AGR': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'FIS' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'PROC' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'PROC' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'PROC' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'PROC' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'QUIM' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'QUIM' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'AGRO' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'AGRO' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'BIO' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'TEC' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'QUIM' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'TEC D' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'HIST' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'HIST' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'LEN' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'LEN' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'TOAMB' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: 'ING' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'BIO' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'LEN' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'LEN' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'TOAMB' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'CIU' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'CIU' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'HIST' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'HIST' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: '----' }
            ],
            '2MB_AGR': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'MAT CTS' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'PROD AN' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'PROD AN' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'APT' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'APT' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'PROD V' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'PROD V' },
                { num_modulo: '10°', dia_semana: 'LUNES', materia: '----' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: 'TO' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'HIST' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'PROD AN' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'PROD AN' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'APT' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'QUIM' },
                { num_modulo: '9°', dia_semana: 'MARTES', materia: '----' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'PROD V' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'PROD V' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: '----' },
                


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: 'TO' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'LEG' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'LEG' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'EMP AGR' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'EMP AGR' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'MAT CTS' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'MAT CTS' },
            

                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'EMPRE' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'EMPRE' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'MAT' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                
            ],
             '3MB_AGR': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'FIS' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'MAT CTS' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'PROY' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'PROY' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'T.O EL A' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'T.O EL A' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: 'SIST A' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: 'SIST A' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: 'FIS' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'ING' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'SOCIO' },
                { num_modulo: '9°', dia_semana: 'MARTES', materia: '----' },
               

                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: 'FILO' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: 'FILO' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: 'SOCIO' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'SOCIO' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'QUIM' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'SIST V' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'SIST V' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'SIST A' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'SIST A' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'MAT CTS' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'MAT CTS' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'AGRO' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'AGRO' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: '----' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'ING' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'EMPR' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'EMPR' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'AGRO' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'AGRO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: 'SIST V' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: 'SIST V' }
            ],
             '1DA_GAS': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'TEC B' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'TEC B' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'TEC B' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'ING' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: 'MAT' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: 'MAT' },


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----|' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '9°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '10°', dia_semana: 'MARTES', materia:'TALLER' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'LEN Y COM' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'ING' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'TALLER' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'TALLER' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: 'TALLER' },
                { num_modulo: '9°', dia_semana: 'MIERCOLES', materia: 'TALLER' },
                { num_modulo: '10°', dia_semana: 'MIERCOLES', materia: 'TALLER' },


                // JUEVES
                { num_modulo: '1°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: 'T.O' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: 'T.O' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: 'LEN Y COM' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'LEN Y COM' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'MARK' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'MARK' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'TEC B' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'TEC B' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'TEC B' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'DIB' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'DIB' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: 'BIO' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: 'BIO' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: '----' }
            ],
            '2DA_GAS': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: 'TEC B' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: 'TEC B' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: 'TEC B' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: 'HIST' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: 'HIST/T.O' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: 'ING/MAT/TO' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: 'ING/MAT' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '10°', dia_semana: 'LUNES', materia: '----' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '9°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '10°', dia_semana: 'MARTES', materia: 'TALLER' },
                { num_modulo: '11°', dia_semana: 'MARTES', materia: 'TALLER' },


                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: 'LEN/TALLER' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: 'TALLER' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: 'TALLER' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: 'TALLER' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: '----' },
                


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: 'LEN.BIOL' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: 'LEN.BIOL' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: 'MARK' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: 'MARK' },
            

                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: 'TEC B' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: 'TEC B' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: 'TEC B' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: 'DIB' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: 'DIB' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                
            ],
             '3DA_GAS': [
                // LUNES
                { num_modulo: '1°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'LUNES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'LUNES', materia: '----' },
                
                


                // MARTES
                { num_modulo: '1°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'MARTES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'MARTES', materia: '----' },
               

                // MIÉRCOLES
                { num_modulo: '1°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'MIERCOLES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'MIERCOLES', materia: '----' },


                // JUEVES
                { num_modulo: '2°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'JUEVES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'JUEVES', materia: '----' },
                


                // VIERNES
                { num_modulo: '1°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '2°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '3°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '4°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '5°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '6°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '7°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '8°', dia_semana: 'VIERNES', materia: '----' },
                { num_modulo: '9°', dia_semana: 'VIERNES', materia: '----' }
            ],



        };


        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            actualizarTabla();
        });


        function actualizarTabla() {
            const cursoSeleccionado = document.getElementById('selectCurso').value;


            // Limpiar todas las celdas previamente cargadas
            document.querySelectorAll('.schedule-table td[data-dia]').forEach(celda => celda.textContent = '');


            // Si existen datos definidos para el curso seleccionado
            if (horariosPorCurso[cursoSeleccionado]) {
                horariosPorCurso[cursoSeleccionado].forEach(clase => {
                    const fila = document.querySelector(`#tablaManana tr[data-mod="${clase.num_modulo}"]`);
                    if (fila) {
                        const celda = fila.querySelector(`td[data-dia="${clase.dia_semana}"]`);
                        if (celda) {
                            celda.textContent = clase.materia;
                        }
                    }
                });
            }
        }


        // Cargar por defecto al abrir la página
        document.addEventListener('DOMContentLoaded', actualizarTabla);
        // ===== Pestañas principales =====
        document.addEventListener('click', function (e) {
            const boton = e.target.closest('.tabs-header .tab-btn');
            if (!boton) return;

            const destino = boton.dataset.tab;
            document.querySelectorAll('.tabs-header .tab-btn').forEach(function (b) {
                b.classList.toggle('active', b === boton);
            });
            document.querySelectorAll('.main-container > .tab-pane').forEach(function (p) {
                const activo = p.id === 'tab-' + destino;
                p.classList.toggle('active', activo);
                p.style.display = activo ? 'block' : 'none';
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
    // Verificar si la URL contiene parámetros de envío de contacto
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('enviado') || urlParams.has('error')) {
        // Activar visualmente la pestaña de atención al público
        const btnAtencion = document.querySelector('.tab-btn[data-tab="atencion"]');
        const paneAtencion = document.getElementById('tab-atencion');
        const btnHorarios = document.querySelector('.tab-btn[data-tab="horarios"]');
        const paneHorarios = document.getElementById('tab-horarios');

        if (btnAtencion && paneAtencion) {
            document.querySelectorAll('.tabs-header .tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.main-container > .tab-pane').forEach(p => {
                p.classList.remove('active');
                p.style.display = 'none';
            });

            btnAtencion.classList.add('active');
            paneAtencion.classList.add('active');
            paneAtencion.style.display = 'block';
        }
    } else {
        actualizarTabla();
    }
});
    </script>

</body>
</html>
