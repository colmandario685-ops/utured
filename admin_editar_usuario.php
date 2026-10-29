<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_GET['id'] ?? null;
if (!$id_usuario) {
    header("Location: admin_dashboard.php");
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $rol = trim($_POST['rol'] ?? 'estudiante');
    
    if (!empty($nombre) && !empty($correo)) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = ?, correo = ?, rol = ? WHERE id_usuario = ?");
        if ($stmt->execute([$nombre, $correo, $rol, $id_usuario])) {
            echo "<script>alert('Usuario actualizado correctamente.'); window.location.href='admin_dashboard.php';</script>";
            exit();
        } else {
            $mensaje = "Error al actualizar los datos.";
        }
    } else {
        $mensaje = "Completa todos los campos obligatorios.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - UTUred</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-card { max-width: 500px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
        .btn-save { background: #0f3273; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #0f3273; text-decoration: none; font-weight: 600; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Editar Usuario: <?php echo htmlspecialchars($usuario['nombre_completo']); ?></h2>
        <?php if (!empty($mensaje)): ?><p style="color: red;"><?php echo $mensaje; ?></p><?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
            </div>
            <div class="form-group">
                <label>Rol del Sistema</label>
                <select name="rol">
                    <option value="estudiante" <?php if($usuario['rol']=='estudiante') echo 'selected'; ?>>Estudiante</option>
                    <option value="admin" <?php if($usuario['rol']=='admin') echo 'selected'; ?>>Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn-save">Guardar Cambios</button>
        </form>
        
        <a href="admin_dashboard.php" class="back-link">← Volver al panel</a>
    </div>
</body>
</html>