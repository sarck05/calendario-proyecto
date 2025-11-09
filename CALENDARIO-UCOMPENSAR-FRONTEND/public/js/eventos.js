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
            $('#detalleIdEvento').val(event.id); // 👈 Guarda el ID para inscribirse

            $('#eventDetailsModal').modal('show');
        } else {
            alert('❌ No se pudo encontrar la información del evento.');
        }
    });

    // 3️⃣ CUANDO HACE CLIC EN “INSCRIBIRSE”
    $(document).on('click', '#btnInscribirse', function () {
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

        // 4️⃣ Enviar la inscripción al servidor
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
