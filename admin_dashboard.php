<?php
session_start();
require_once 'db.php';

// Verificación de seguridad: Solo administradores
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

$id_admin = $_SESSION['usuario_id'];
$nombre_admin = $_SESSION['nombre'] ?? 'Administrador';

// Obtener todos los usuarios registrados
$stmtUsuarios = $pdo->query("SELECT id_usuario, ci, nombre_completo, correo, rol, fecha_nacimiento FROM usuarios ORDER BY id_usuario DESC");
$usuarios = $stmtUsuarios->fetchAll();

// Obtener todos los mensajes de contacto recibidos
$stmtMensajes = $pdo->query("SELECT id_mensaje, nombre, email, asunto, mensaje, fecha FROM mensajes_contacto ORDER BY fecha DESC");
$mensajes = $stmtMensajes->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTUred - Panel de Administración</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .admin-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .tab-btn { background: #f1f5f9; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; color: #64748b; }
        .tab-btn.active { background: #0f3273; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .table-admin { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .table-admin th, .table-admin td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        .table-admin th { background-color: #0f3273; color: white; }
        .btn-action { padding: 6px 12px; border-radius: 4px; font-size: 0.8rem; text-decoration: none; font-weight: bold; cursor: pointer; border: none; }
        .btn-edit { background-color: #3b82f6; color: white; }
        .btn-delete { background-color: #ef4444; color: white; }
        .btn-reply { background-color: #10b981; color: white; }
        .message-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; margin-bottom: 15px; }
        .reply-form { margin-top: 10px; display: flex; gap: 10px; }
        .reply-form input { flex: 1; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="logo-section">
            <div class="logo-icon">UTU</div>
            <div class="logo-text">
                <span class="brand-title">ADMINISTRACIÓN</span>
                <span class="brand-sub">Panel de Control UTUred</span>
            </div>
        </div>
        <div class="user-profile">
            <span>Admin: <strong><?php echo htmlspecialchars($nombre_admin); ?></strong></span>
            <a href="auth_controller.php?action=logout" style="color: #ffcccc; margin-left: 15px; text-decoration: none;">Cerrar sesión</a>
        </div>
    </header>

    <div class="admin-container">
        <h1>Panel de Gestión Institucional</h1>
        
        <!-- Pestañas de Navegación del Panel -->
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="switchTab(event, 'usuariosTab')">👥 Gestión de Usuarios</button>
            <button class="tab-btn" onclick="switchTab(event, 'mensajesTab')">📬 Mensajes de Contacto</button>
        </div>

        <!-- SECCIÓN 1: GESTIÓN DE USUARIOS -->
        <div id="usuariosTab" class="tab-content active">
            <h2>Usuarios Registrados en el Sistema</h2>
            <div style="overflow-x: auto;">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula (CI)</th>
                            <th>Nombre Completo</th>
                            <th>Correo Electrónico</th>
                            <th>Rol</th>
                            <th>F. Nacimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?php echo $u['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($u['ci']); ?></td>
                            <td><?php echo htmlspecialchars($u['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($u['correo']); ?></td>
                            <td><strong><?php echo htmlspecialchars($u['rol']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['fecha_nacimiento']); ?></td>
                            <td>
                                <a href="admin_editar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn-action btn-edit">Editar</a>
                                <?php if ($u['id_usuario'] != $id_admin): ?>
                                    <a href="admin_actions.php?action=eliminar_usuario&id=<?php echo $u['id_usuario']; ?>" class="btn-action btn-delete" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN 2: MENSAJES DE CONTACTO -->
        <div id="mensajesTab" class="tab-content">
            <h2>Bandeja de Mensajes Recibidos</h2>
            <?php if (empty($mensajes)): ?>
                <p>No hay mensajes de contacto recibidos por el momento.</p>
            <?php else: ?>
                <?php foreach ($mensajes as $msg): ?>
                    <div class="message-box">
                        <p><strong>De:</strong> <?php echo htmlspecialchars($msg['nombre']); ?> (&lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;)</p>
                        <p><strong>Asunto:</strong> <?php echo htmlspecialchars($msg['asunto']); ?> <small style="float: right; color: #64748b;"><?php echo $msg['fecha']; ?></small></p>
                        <p><strong>Mensaje:</strong><br><?php echo nl2br(htmlspecialchars($msg['mensaje'])); ?></p>
                        
                        <!-- Formulario para responder por correo -->
                        <form action="admin_actions.php?action=responder_mensaje" method="POST" class="reply-form">
                            <input type="hidden" name="email_destino" value="<?php echo htmlspecialchars($msg['email']); ?>">
                            <input type="hidden" name="nombre_destino" value="<?php echo htmlspecialchars($msg['nombre']); ?>">
                            <input type="text" name="respuesta" placeholder="Escribe una respuesta para enviar a su correo..." required>
                            <button type="submit" class="btn-action btn-reply">Enviar Respuesta</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <script>
        function switchTab(evt, tabId) {
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(c => c.classList.remove('active'));

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(b => b.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>