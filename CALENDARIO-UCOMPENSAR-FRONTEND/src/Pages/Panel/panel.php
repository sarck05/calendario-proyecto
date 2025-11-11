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

/* =====================================================
   CRUD DE EVENTOS
===================================================== */

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
        $stmt = $conn->prepare("SELECT imagen FROM eventos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($imagen_actual);
        $stmt->fetch();
        $stmt->close();

        if (empty($imagen_nombre)) {
            $imagen_nombre = $imagen_actual;
        }

        $stmt = $conn->prepare("UPDATE eventos SET nombre=?, descripcion=?, tipo=?, fecha=?, imagen=? WHERE id=?");
        $stmt->bind_param("sssssi", $nombre, $descripcion, $tipo, $fecha, $imagen_nombre, $id);
        $stmt->execute();
        $stmt->close();

        $conn->query("DELETE FROM evento_etiquetas WHERE evento_id=$id");
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

/* =====================================================
   CRUD DE NOTICIAS
===================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_news'])) {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $es_urgente = isset($_POST['es_urgente']) ? 1 : 0;

    $imagen_nombre = null;
    if (isset($_FILES['imagen_noticia']) && $_FILES['imagen_noticia']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = realpath(__DIR__ . '/../../../public') . '/uploads/noticias/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['imagen_noticia']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = uniqid('noticia_') . '.' . strtolower($ext);
        $ruta_destino = $upload_dir . $imagen_nombre;
        move_uploaded_file($_FILES['imagen_noticia']['tmp_name'], $ruta_destino);
    }

    $stmt = $conn->prepare("INSERT INTO noticias (titulo, contenido, imagen, es_urgente, fecha) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssi", $titulo, $contenido, $imagen_nombre, $es_urgente);
    $stmt->execute();
    $stmt->close();

    $_SESSION['mensaje'] = "📰 Noticia creada con éxito.";
    header("Location: panel.php");
    exit();
}

if (isset($_GET['delete_news'])) {
    $id = (int)$_GET['delete_news'];
    $conn->query("DELETE FROM noticias WHERE id = $id");
    $_SESSION['mensaje'] = "🗑️ Noticia eliminada.";
    header("Location: panel.php");
    exit();
}

$etiquetas_disponibles = $conn->query("SELECT * FROM etiquetas ORDER BY nombre");
$noticias = $conn->query("SELECT * FROM noticias ORDER BY fecha DESC");
$action = $_GET['action'] ?? 'list';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Eventos y Noticias</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/public/css/panelCentral.css">
</head>
<body>
<?php include('../../Components/menu.php'); ?>

<div class="container" style="margin-top:20px">
    <header>
        <h1><i class="bi bi-calendar-event"></i> Panel de Gestión de Eventos y Noticias</h1>
    </header>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
        </div>
    <?php endif; ?>

    <main>
        <!-- ==================== EVENTOS ==================== -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="bi bi-calendar3"></i> Gestión de Eventos
            </div>
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
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================== NOTICIAS ==================== -->
        <div class="panel panel-default" style="margin-top:40px;">
            <div class="panel-heading">
                <i class="bi bi-newspaper"></i> Gestión de Noticias
            </div>
            <div class="panel-body">
                <form action="panel.php" method="POST" enctype="multipart/form-data" class="mb-4">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Contenido</label>
                        <textarea name="contenido" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Imagen</label>
                        <input type="file" name="imagen_noticia" class="form-control">
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="es_urgente"> Es urgente</label>
                    </div>
                    <button type="submit" name="save_news" class="btn btn-success"><i class="bi bi-plus-circle"></i> Crear Noticia</button>
                </form>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Contenido</th>
                            <th>Imagen</th>
                            <th>Urgente</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($n = $noticias->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($n['titulo']) ?></td>
                            <td><?= htmlspecialchars(substr($n['contenido'], 0, 80)) ?>...</td>
                            <td>
                                <?php if ($n['imagen']): ?>
                                    <img src="/uploads/noticias/<?= htmlspecialchars($n['imagen']) ?>" width="80">
                                <?php endif; ?>
                            </td>
                            <td><?= $n['es_urgente'] ? '🔥 Sí' : 'No' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></td>
                            <td>
                                <a href="panel.php?delete_news=<?= $n['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar noticia?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
</body>
</html>
