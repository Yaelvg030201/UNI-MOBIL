<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=localhost;dbname=unimovil;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error conexión base de datos: " . $e->getMessage());
}

// Insertar nuevo viaje si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origen = $_POST['origen'] ?? '';
    $destino = $_POST['destino'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $usuario_email = $_SESSION['email'];

    // Validar campos simples
    if ($origen && $destino && $fecha) {
        $stmt = $pdo->prepare("INSERT INTO viajes (origen, destino, fecha, usuario_email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$origen, $destino, $fecha, $usuario_email]);
        $mensaje = "Viaje creado correctamente.";
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}

// Obtener viajes disponibles
$stmt = $pdo->query("SELECT * FROM viajes ORDER BY fecha ASC");
$viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Viajes - UniMobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-4">
        <h1>Viajes disponibles</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulario para crear nuevo viaje -->
        <div class="card mb-4">
            <div class="card-header">Crear nuevo viaje</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="origen" class="form-label">Origen</label>
                        <input type="text" class="form-control" id="origen" name="origen" required />
                    </div>
                    <div class="mb-3">
                        <label for="destino" class="form-label">Destino</label>
                        <input type="text" class="form-control" id="destino" name="destino" required />
                    </div>
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" required />
                    </div>
                    <button type="submit" class="btn btn-primary">Crear viaje</button>
                </form>
            </div>
        </div>

        <!-- Lista de viajes disponibles -->
        <h2>Viajes disponibles</h2>
        <?php if (count($viajes) === 0): ?>
            <p>No hay viajes disponibles.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Creado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($viajes as $viaje): ?>
                        <tr>
                            <td><?= htmlspecialchars($viaje['origen']) ?></td>
                            <td><?= htmlspecialchars($viaje['destino']) ?></td>
                            <td><?= htmlspecialchars($viaje['fecha']) ?></td>
                            <td><?= htmlspecialchars($viaje['usuario_email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-3">← Volver al inicio</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

