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

// --- LÓGICA PRINCIPAL ---

// Variable para almacenar mensajes de éxito o error
 $mensaje = '';

// Manejo de acciones POST (Crear y Actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $fecha = $_POST['fecha'];
    $etiquetas_ids = isset($_POST['etiquetas']) ? $_POST['etiquetas'] : [];

    if ($id > 0) {
        // --- ACTUALIZAR EVENTO EXISTENTE ---
        $stmt = $conn->prepare("UPDATE eventos SET nombre = ?, descripcion = ?, tipo = ?, fecha = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $descripcion, $tipo, $fecha, $id);
        $stmt->execute();
        $stmt->close();

        // Actualizar etiquetas: primero eliminar las existentes y luego insertar las nuevas
        $conn->query("DELETE FROM evento_etiquetas WHERE evento_id = $id");
        foreach ($etiquetas_ids as $etiqueta_id) {
            $stmt = $conn->prepare("INSERT INTO evento_etiquetas (evento_id, etiqueta_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $id, $etiqueta_id);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['mensaje'] = "Evento actualizado con éxito.";

    } else {
        // --- CREAR NUEVO EVENTO ---
        $stmt = $conn->prepare("INSERT INTO eventos (nombre, descripcion, tipo, fecha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $descripcion, $tipo, $fecha);
        $stmt->execute();
        $stmt->close();

        $nuevo_evento_id = $conn->insert_id;

        // Insertar las etiquetas seleccionadas
        foreach ($etiquetas_ids as $etiqueta_id) {
            $stmt = $conn->prepare("INSERT INTO evento_etiquetas (evento_id, etiqueta_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $nuevo_evento_id, $etiqueta_id);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['mensaje'] = "Evento creado con éxito.";
    }
    
    // Redirigir para evitar el reenvío del formulario al actualizar la página
    header("Location: panel.php");
    exit();
}

// Manejo de acción GET (Eliminar)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Iniciar transacción para asegurar que todas las operaciones se completen
    $conn->begin_transaction();
    try {
        // Eliminar primero las relaciones en evento_etiquetas (clave foránea)
        $conn->query("DELETE FROM evento_etiquetas WHERE evento_id = $id");
        // Luego eliminar el evento
        $conn->query("DELETE FROM eventos WHERE id = $id");
        $conn->commit();
        $_SESSION['mensaje'] = "Evento eliminado con éxito.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['mensaje'] = "Error al eliminar el evento: " . $e->getMessage();
    }
    
    header("Location: panel.php");
    exit();
}

// Obtener todas las etiquetas para los formularios
 $etiquetas_disponibles = $conn->query("SELECT * FROM etiquetas ORDER BY nombre");

// Determinar la vista a mostrar
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

    // Obtener las etiquetas del evento a editar
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
    <title>Panel de Gestión de Eventos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f9; color: #333; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1, h2 { color: #4a4a4a; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .btn { display: inline-block; padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; }
        .btn:hover { opacity: 0.9; }
        .message { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background: #d4edda; color: #155724; border-color: #c3e6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="datetime-local"], textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .checkbox-group label { font-weight: normal; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>Panel de Gestión de Eventos</h1>
    </header>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="message">
            <?php 
                echo $_SESSION['mensaje']; 
                unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>

    <main>
        <?php if ($action === 'edit' || $action === 'create'): ?>
            <!-- FORMULARIO DE CREACIÓN/EDICIÓN -->
            <h2><?php echo $action === 'edit' ? 'Editar Evento' : 'Crear Nuevo Evento'; ?></h2>
            <form action="panel.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $evento_a_editar['id'] ?? ''; ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre del Evento:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($evento_a_editar['nombre'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($evento_a_editar['descripcion'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($evento_a_editar['tipo'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha y Hora:</label>
                    <input type="datetime-local" id="fecha" name="fecha" value="<?php echo date('Y-m-d\TH:i', strtotime($evento_a_editar['fecha'] ?? 'now')); ?>" required>
                </div>

                <div class="form-group checkbox-group">
                    <label>Etiquetas:</label>
                    <?php while ($etiqueta = $etiquetas_disponibles->fetch_assoc()): ?>
                        <div style="margin-bottom: 5px;">
                            <input type="checkbox" name="etiquetas[]" value="<?php echo $etiqueta['id']; ?>" 
                                <?php echo (in_array($etiqueta['id'], $etiquetas_del_evento)) ? 'checked' : ''; ?>>
                            <label for="etiqueta_<?php echo $etiqueta['id']; ?>"><?php echo htmlspecialchars($etiqueta['nombre']); ?></label>
                        </div>
                    <?php endwhile; ?>
                    <?php 
                        // Reiniciar el puntero del resultado para poder usarlo de nuevo si es necesario
                        $etiquetas_disponibles->data_seek(0); 
                    ?>
                </div>

                <button type="submit" name="save_event" class="btn">Guardar Evento</button>
                <a href="panel.php" class="btn" style="background-color: #6c757d;">Cancelar</a>
            </form>
        <?php else: ?>
            <!-- LISTADO DE EVENTOS -->
            <a href="panel.php?action=create" class="btn">Crear Nuevo Evento</a>

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Fecha y Hora</th>
                        <th>Etiquetas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reconectar para la lista de eventos
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
                            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['etiquetas_nombres']) . "</td>";
                            echo "<td>";
                            echo "<a href='panel.php?action=edit&id=" . $row['id'] . "' class='btn btn-edit'>Editar</a> ";
                            echo "<a href='panel.php?action=delete&id=" . $row['id'] . "' class='btn btn-delete' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este evento?\");'>Eliminar</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay eventos registrados.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</div>

</body>
</html>