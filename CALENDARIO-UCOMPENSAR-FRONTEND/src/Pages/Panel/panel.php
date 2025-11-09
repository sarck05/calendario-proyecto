<?php
session_start();

// --- CONFIGURACIÓN DE LA BASE DE DATOS ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestioneventos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $fecha = $_POST['fecha'];
    $etiquetas_ids = isset($_POST['etiquetas']) ? $_POST['etiquetas'] : [];

    // --- MANEJO DE IMAGEN ---
    $imagen_nombre = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = realpath(__DIR__ . '/../../../public') . '/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = uniqid('evento_') . '.' . strtolower($ext);
        $ruta_destino = $upload_dir . $imagen_nombre;

        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino);
    }

    if ($id > 0) {
        // Si no se sube nueva imagen, mantener la existente
        $stmt = $conn->prepare("SELECT imagen FROM eventos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($imagen_actual);
        $stmt->fetch();
        $stmt->close();

        if (empty($imagen_nombre)) {
            $imagen_nombre = $imagen_actual;
        }

        $stmt = $conn->prepare("UPDATE eventos SET nombre = ?, descripcion = ?, tipo = ?, fecha = ?, imagen = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nombre, $descripcion, $tipo, $fecha, $imagen_nombre, $id);
        $stmt->execute();
        $stmt->close();

        $conn->query("DELETE FROM evento_etiquetas WHERE evento_id = $id");
        foreach ($etiquetas_ids as $etiqueta_id) {
            $stmt = $conn->prepare("INSERT INTO evento_etiquetas (evento_id, etiqueta_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $id, $etiqueta_id);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['mensaje'] = "✨ Evento actualizado con éxito.";
    } else {
        $stmt = $conn->prepare("INSERT INTO eventos (nombre, descripcion, tipo, fecha, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $descripcion, $tipo, $fecha, $imagen_nombre);
        $stmt->execute();
        $nuevo_evento_id = $stmt->insert_id;
        $stmt->close();

        foreach ($etiquetas_ids as $etiqueta_id) {
            $stmt = $conn->prepare("INSERT INTO evento_etiquetas (evento_id, etiqueta_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $nuevo_evento_id, $etiqueta_id);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['mensaje'] = "🎉 Evento creado con éxito.";
    }

    header("Location: panel.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->begin_transaction();
    try {
        $conn->query("DELETE FROM evento_etiquetas WHERE evento_id = $id");
        $conn->query("DELETE FROM eventos WHERE id = $id");
        $conn->commit();
        $_SESSION['mensaje'] = "🗑️ Evento eliminado con éxito.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['mensaje'] = "Error al eliminar el evento: " . $e->getMessage();
    }
    header("Location: panel.php");
    exit();
}

$etiquetas_disponibles = $conn->query("SELECT * FROM etiquetas ORDER BY nombre");
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$evento_a_editar = null;
$etiquetas_del_evento = [];

if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $evento_a_editar = $resultado->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("SELECT etiqueta_id FROM evento_etiquetas WHERE evento_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($row = $resultado->fetch_assoc()) {
        $etiquetas_del_evento[] = $row['etiqueta_id'];
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Eventos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/public/css/panelCentral.css">
</head>
<body>
<?php include('../../Components/menu.php'); ?>

<div class="container" style="margin-top:20px">
    <header>
        <h1><i class="bi bi-calendar-event"></i> Panel de Gestión de Eventos</h1>
    </header>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
        </div>
    <?php endif; ?>

    <main>
        <?php if ($action === 'edit' || $action === 'create'): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="bi bi-pencil-square"></i>
                    <?php echo $action === 'edit' ? 'Editar Evento' : 'Crear Nuevo Evento'; ?>
                </div>
                <div class="panel-body">
                    <form action="panel.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $evento_a_editar['id'] ?? ''; ?>">

                        <div class="form-group">
                            <label>Nombre del Evento:</label>
                            <input type="text" class="form-control" name="nombre" required
                                   value="<?php echo htmlspecialchars($evento_a_editar['nombre'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Descripción:</label>
                            <textarea class="form-control" name="descripcion" rows="3"><?php echo htmlspecialchars($evento_a_editar['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Imagen del evento:</label>
                            <?php if (!empty($evento_a_editar['imagen'])): ?>
                                <div class="mb-2">
                                    <img src="/uploads/<?php echo htmlspecialchars($evento_a_editar['imagen']); ?>" class="img-thumbnail" style="max-width: 200px;">
                            <?php endif; ?>
                            <input type="file" class="form-control" name="imagen" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label>Tipo:</label>
                            <input type="text" class="form-control" name="tipo"
                                   value="<?php echo htmlspecialchars($evento_a_editar['tipo'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Fecha y hora:</label>
                            <input type="datetime-local" class="form-control" name="fecha"
                                   value="<?php echo !empty($evento_a_editar['fecha']) ? date('Y-m-d\TH:i', strtotime($evento_a_editar['fecha'])) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Etiquetas:</label><br>
                            <?php
                            $etiquetas_disponibles->data_seek(0);
                            while ($etiqueta = $etiquetas_disponibles->fetch_assoc()):
                                $checked = in_array($etiqueta['id'], $etiquetas_del_evento) ? 'checked' : '';
                                echo "<label class='checkbox-inline'><input type='checkbox' name='etiquetas[]' value='{$etiqueta['id']}' $checked> {$etiqueta['nombre']}</label>";
                            endwhile;
                            ?>
                        </div>

                        <button type="submit" name="save_event" class="btn btn-success">
                            <i class="bi bi-save"></i> Guardar Evento
                        </button>
                        <a href="panel.php" class="btn btn-default">Cancelar</a>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <a href="panel.php?action=create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Crear Nuevo Evento
                    </a>
                    <table class="table table-hover" style="margin-top:20px;">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Etiquetas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        $sql = "SELECT e.*, GROUP_CONCAT(et.nombre SEPARATOR ', ') AS etiquetas_nombres
                                FROM eventos e
                                LEFT JOIN evento_etiquetas ee ON e.id = ee.evento_id
                                LEFT JOIN etiquetas et ON ee.etiqueta_id = et.id
                                GROUP BY e.id
                                ORDER BY e.fecha ASC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>";
                                if (!empty($row['imagen'])) {
                                    echo "<img src='../../../public/uploads/" . htmlspecialchars($row['imagen']) . "' style='width:80px;height:auto;border-radius:6px;'>";
                                } else {
                                    echo "—";
                                }
                                echo "</td>";
                                echo "<td><strong>" . htmlspecialchars($row['nombre']) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha'])) . "</td>";
                                echo "<td>" . htmlspecialchars($row['etiquetas_nombres']) . "</td>";
                                echo "<td> 
                                        <a href='panel.php?action=edit&id={$row['id']}' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a>
                                        <a href='panel.php?action=delete&id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Seguro que deseas eliminar este evento?\");'><i class='bi bi-trash'></i></a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>🦕 No hay eventos registrados</td></tr>";
                        }
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
