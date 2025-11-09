<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../../config/conexion.php');

// --- OBTENER TODOS LOS EVENTOS ---
 $sql = "SELECT
            e.id, e.nombre, e.descripcion, e.tipo, e.fecha, e.imagen,
            GROUP_CONCAT(et.nombre SEPARATOR ', ') AS etiquetas
        FROM eventos e
        LEFT JOIN evento_etiquetas ee ON e.id = ee.evento_id
        LEFT JOIN etiquetas et ON ee.etiqueta_id = et.id
        GROUP BY e.id
        ORDER BY e.fecha ASC";
 $result = $conn->query($sql);

 $events_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $timestamp = strtotime($row['fecha']);
        $events_data[] = [
            'id' => (int)$row['id'],
            'title' => $row['nombre'],
            'description' => $row['descripcion'],
            'event_date' => date('Y-m-d', $timestamp),
            'event_time' => date('H:i', $timestamp),
            'category' => $row['tipo'],
            'etiquetas' => $row['etiquetas'] ?: 'Sin etiquetas',
            'image' => $row['imagen']
            ? '../../../public/uploads/' . htmlspecialchars($row['imagen'])
            : "https://picsum.photos/seed/evento{$row['id']}/600/400.jpg",
        ];
    }
}

 $conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos UCompensar</title>
    <!-- Fuentes modernas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Librerías (Bootstrap 3) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Tu CSS mejorado -->
    <link rel="stylesheet" href="../../../public/css/eventos.css">
</head>
<body class="Eventos">
    <!-- MENU -->
    <?php include('../../Components/menu.php'); ?>
    <!-- FIN MENU -->

    <!-- Input oculto con el ID del usuario logueado -->
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <input type="hidden" id="usuario_id" value="<?php echo $_SESSION['usuario_id']; ?>">
    <?php else: ?>
        <input type="hidden" id="usuario_id" value="">
    <?php endif; ?>

    <div class="container mt-5">
        <div class="header text-center mb-4">
            <h1>EVENTOS UCOMPENSAR</h1>
            <p>Descubre los próximos eventos en nuestra universidad</p>
        </div>

        <?php if (empty($events_data)): ?>
            <div class="alert alert-warning text-center">
                <h4>No hay eventos disponibles en este momento.</h4>
                <p>Vuelve pronto para conocer nuestras nuevas actividades.</p>
            </div>
        <?php else: ?>
            <div class="row" id="listaEventos">
                <?php foreach ($events_data as $ev): ?>
                    <div class="col-md-4 mb-4">
                        <div class="event-card shadow-sm">
                            <img src="<?php echo $ev['image']; ?>" class="event-image" alt="<?php echo $ev['title']; ?>">
                            <div class="event-body">
                                <div class="event-category"><?php echo htmlspecialchars($ev['category']); ?></div>
                                <h5 class="event-title"><?php echo htmlspecialchars($ev['title']); ?></h5>
                                <p class="event-description"><?php echo htmlspecialchars($ev['description']); ?></p>
                                <div class="event-meta">
                                    <span><i class="bi bi-calendar-event"></i> <?php echo $ev['event_date']; ?></span>
                                    <span><i class="bi bi-clock"></i> <?php echo $ev['event_time']; ?></span>
                                </div>
                                <button class="btn btn-primary details-btn w-100" data-event-id="<?php echo $ev['id']; ?>">Ver Detalles</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Mejorado -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Título del Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Imagen centrada -->
                        <div class="col-md-5">
                            <div class="modal-image-container">
                                <img id="modalImage" src="" class="img-fluid rounded shadow-sm" alt="Imagen del evento">
                            </div>
                        </div>

                        <!-- Descripción y detalles -->
                        <div class="col-md-7">
                            <div class="modal-details-container">
                                <p id="modalDescription">Descripción...</p>
                                <div class="modal-meta">
                                    <p><i class="bi bi-calendar-event"></i> <strong>Fecha:</strong> <span id="modalDate">--</span></p>
                                    <p><i class="bi bi-clock"></i> <strong>Hora:</strong> <span id="modalTime">--</span></p>
                                    <p><i class="bi bi-tag"></i> <strong>Categoría:</strong> <span id="modalCategory">--</span></p>
                                    <p><i class="bi bi-tags"></i> <strong>Etiquetas:</strong> <span id="modalTags">--</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campo oculto -->
                <input type="hidden" id="detalleIdEvento" name="detalleIdEvento" value="">

                <!-- Footer con botones -->
                <div class="modal-footer d-flex justify-content-between">
                    <button id="btnInscribirse" class="btn btn-primary">Inscribirse</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Datos y script -->
    <script id="events-data" type="application/json"><?php echo json_encode($events_data, JSON_UNESCAPED_UNICODE); ?></script>

    <script>
    $(document).ready(function () {
        // 1️⃣ LEER LOS DATOS DEL SCRIPT
        const eventsDataElement = document.getElementById('events-data');
        let events = [];

        if (eventsDataElement) {
            try {
                events = JSON.parse(eventsDataElement.textContent);
                console.log("✅ Eventos cargados correctamente:", events);
            } catch (e) {
                console.error("❌ Error al parsear el JSON de eventos:", e);
            }
        }

        // 2️⃣ ABRIR EL MODAL CON DETALLES
        $('.details-btn').on('click', function () {
            const eventId = parseInt($(this).data('event-id'));
            const event = events.find(e => e.id === eventId);

            if (event) {
                $('#modalTitle').text(event.title);
                $('#modalImage').attr('src', event.image);
                $('#modalDescription').text(event.description);
                $('#modalDate').text(event.event_date);
                $('#modalTime').text(event.event_time);
                $('#modalCategory').text(event.category);
                $('#modalTags').text(event.etiquetas);
                $('#detalleIdEvento').val(event.id);
                $('#eventDetailsModal').modal('show');
            } else {
                alert('❌ No se pudo encontrar la información del evento.');
            }
        });

        // 3️⃣ CUANDO HACE CLIC EN "INSCRIBIRSE"
        $('#btnInscribirse').on('click', function () {
            const usuarioIdEl = document.getElementById('usuario_id');
            const usuarioId = usuarioIdEl ? usuarioIdEl.value.trim() : '';
            const eventoId = document.getElementById('detalleIdEvento').value;

            if (!eventoId) {
                alert('⚠️ No se pudo obtener el ID del evento.');
                return;
            }

            if (!usuarioId) {
                alert('⚠️ Por favor, inicia sesión antes de inscribirte.');
                return;
            }

            // 4️⃣ Enviar inscripción
            fetch('inscribirse_evento.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'usuario_id=' + encodeURIComponent(usuarioId) +
                      '&evento_id=' + encodeURIComponent(eventoId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    alert('✅ Te has inscrito correctamente al evento.');
                    $('#eventDetailsModal').modal('hide');
                } else if (data.status === 'exists') {
                    alert('⚠️ Ya estás inscrito en este evento.');
                } else {
                    alert('❌ Ocurrió un error al inscribirte.');
                }
            })
            .catch(err => {
                console.error('Error en la inscripción:', err);
                alert('❌ No se pudo conectar con el servidor.');
            });
        });
    });
    </script>
</body>
</html>