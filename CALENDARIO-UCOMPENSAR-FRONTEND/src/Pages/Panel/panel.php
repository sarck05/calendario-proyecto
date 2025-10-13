<?php
session_start();

// --- CONFIGURACIÓN DE LA BASE DE DATOS ---
// Modifica estos valores con los de tu servidor
 $servername = "localhost";
 $username = "root"; // Tu usuario de la base de datos
 $password = ""; // Tu contraseña de la base de datos
 $dbname = "gestioneventos"; // El nombre de tu base de datos

// Crear conexión
 $conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// --- LÓGICA PRINCIPAL (Sin cambios) ---

 $mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $fecha = $_POST['fecha'];
    $etiquetas_ids = isset($_POST['etiquetas']) ? $_POST['etiquetas'] : [];

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE eventos SET nombre = ?, descripcion = ?, tipo = ?, fecha = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $descripcion, $tipo, $fecha, $id);
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
        $stmt = $conn->prepare("INSERT INTO eventos (nombre, descripcion, tipo, fecha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $descripcion, $tipo, $fecha);
        $stmt->execute();
        $stmt->close();
        $nuevo_evento_id = $conn->insert_id;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Eventos</title>
    
    <!-- Bootstrap 3.4.1 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Fonts: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <link rel="stylesheet" href="/public/css/panelCentral.css">
</head>
<body>
<!-- MENU -->
<?php include('../../Components/menu.php'); ?>
<!-- FIN MENU -->

<div class="container" style="margin-top:20px">
    <header>
        <h1 class="main-header"><i class="fas fa-calendar-star"></i> Panel de Gestión de Eventos</h1>
    </header>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible alert-animate" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php 
                echo $_SESSION['mensaje']; 
                unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>

    <main>
        <?php if ($action === 'edit' || $action === 'create'): ?>
            <!-- FORMULARIO DE CREACIÓN/EDICIÓN -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fas fa-<?php echo $action === 'edit' ? 'edit' : 'plus-circle'; ?>"></i> 
                    <?php echo $action === 'edit' ? 'Editar Evento' : 'Crear Nuevo Evento'; ?>
                </div>
                <div class="panel-body">
                    <form action="panel.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $evento_a_editar['id'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label for="nombre"><i class="fas fa-signature"></i> Nombre del Evento:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($evento_a_editar['nombre'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion"><i class="fas fa-align-left"></i> Descripción:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($evento_a_editar['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tipo"><i class="fas fa-tag"></i> Tipo:</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" value="<?php echo htmlspecialchars($evento_a_editar['tipo'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="fecha"><i class="fas fa-clock"></i> Fecha y Hora:</label>
                            <input type="datetime-local" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d\TH:i', strtotime($evento_a_editar['fecha'] ?? 'now')); ?>" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-tags"></i> Etiquetas:</label>
                            <?php 
                                $etiquetas_disponibles->data_seek(0); 
                                while ($etiqueta = $etiquetas_disponibles->fetch_assoc()): 
                            ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="etiquetas[]" value="<?php echo $etiqueta['id']; ?>"
                                            <?php echo (in_array($etiqueta['id'], $etiquetas_del_evento)) ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($etiqueta['nombre']); ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <button type="submit" name="save_event" class="btn btn-success btn-animate">
                            <i class="fas fa-save"></i> Guardar Evento
                        </button>
                        <a href="panel.php" class="btn btn-default btn-animate">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </a>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- LISTADO DE EVENTOS -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <a href="panel.php?action=create" class="btn btn-primary btn-animate">
                        <i class="fas fa-plus-circle"></i> Crear Nuevo Evento
                    </a>

                    <table class="table table-hover" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th><i class="fas fa-signature"></i> Nombre</th>
                                <th><i class="fas fa-align-left"></i> Descripción</th>
                                <th><i class="fas fa-tag"></i> Tipo</th>
                                <th><i class="fas fa-clock"></i> Fecha y Hora</th>
                                <th><i class="fas fa-tags"></i> Etiquetas</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
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
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($row['nombre']) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                    echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['etiquetas_nombres']) . "</td>";
                                    echo "<td>";
                                    echo "<a href='panel.php?action=edit&id=" . $row['id'] . "' class='btn btn-warning btn-sm btn-animate'><i class='fas fa-edit'></i></a> ";
                                    echo "<a href='panel.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger btn-sm btn-animate' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este evento? No hay vuelta atrás.\");'><i class='fas fa-trash-alt'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center' style='padding: 40px;'>🦕 No hay eventos registrados. ¡Crea el primero!</td></tr>";
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