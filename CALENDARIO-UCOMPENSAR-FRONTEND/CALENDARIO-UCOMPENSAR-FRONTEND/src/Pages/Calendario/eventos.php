<?php
// --- SIMULACIÓN DE CONEXIÓN A BASE DE DATOS Y OBTENCIÓN DE EVENTOS ---
// En un caso real, aquí conectarías a tu base de datos MySQL, por ejemplo:
/*
 $conn = new mysqli("localhost", "usuario", "contraseña", "nombre_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
 $sql = "SELECT id, title, description, event_date, event_time, location, price, category, image_url FROM events ORDER BY event_date ASC";
 $result = $conn->query($sql);
 $events = $result->fetch_all(MYSQLI_ASSOC);
 $conn->close();
*/

// Para este ejemplo, usaremos un array de PHP para simular los datos de la BD.
 $events_data = [
    [
        'id' => 1,
        'title' => "Conferencia de Inteligencia Artificial",
        'description' => "Explora las últimas tendencias en IA y su aplicación en diversos campos. Aprende de expertos sobre el futuro de la tecnología, el aprendizaje automático y cómo la IA está transformando industrias enteras. Una oportunidad única para estudiantes y profesionales.",
        'event_date' => "2024-06-15",
        'event_time' => "10:00 AM",
        'location' => "Auditorio Principal",
        'price' => "Gratis",
        'category' => "academic",
        'image_url' => "https://picsum.photos/seed/ai-conference/600/400.jpg"
    ],
    [
        'id' => 2,
        'title' => "Festival de Cine Universitario",
        'description' => "Disfruta de una selección de películas realizadas por estudiantes de cine. Apoya el talento emergente y sumérgete en historias creativas que reflejan la visión de la nueva generación de cineastas. Habrá sesiones de Q&A con los directores.",
        'event_date' => "2024-06-22",
        'event_time' => "6:00 PM",
        'location' => "Teatro Universitario",
        'price' => "$5.000",
        'category' => "cultural",
        'image_url' => "https://picsum.photos/seed/film-festival/600/400.jpg"
    ],
    [
        'id' => 3,
        'title' => "Torneo de Baloncesto",
        'description' => "Participa o anima a tu equipo en el torneo interfacultades más emocionante del año. Vive la pasión del deporte universitario, disfruta de partidos reñidos y celebra el espíritu competitivo y de camaradería.",
        'event_date' => "2024-06-28",
        'event_time' => "3:00 PM",
        'location' => "Polideportivo",
        'price' => "Gratis",
        'category' => "sports",
        'image_url' => "https://picsum.photos/seed/basketball/600/400.jpg"
    ],
    [
        'id' => 4,
        'title' => "Taller de Emprendimiento",
        'description' => "Aprende las claves para lanzar tu propio negocio de la mano de expertos. Desde la idea inicial hasta el plan de negocio y la búsqueda de financiación. Transforma tu visión en una empresa exitosa.",
        'event_date' => "2024-07-05",
        'event_time' => "9:00 AM",
        'location' => "Sala de Conferencias B",
        'price' => "$10.000",
        'category' => "academic",
        'image_url' => "https://picsum.photos/seed/entrepreneurship/600/400.jpg"
    ],
    [
        'id' => 5,
        'title' => "Concierto de Música Clásica",
        'description' => "Disfruta de una noche de música clásica interpretada por la prestigiosa orquesta universitaria. Un viaje a través de obras maestras de compositores legendarios en un ambiente inmejorable.",
        'event_date' => "2024-07-12",
        'event_time' => "7:30 PM",
        'location' => "Auditorio Principal",
        'price' => "$15.000",
        'category' => "cultural",
        'image_url' => "https://picsum.photos/seed/classical-music/600/400.jpg"
    ],
    [
        'id' => 6,
        'title' => "Día de la Integración",
        'description' => "Únete a nosotros para un día lleno de actividades, juegos y comida. Una jornada perfecta para conocer a tus compañeros, hacer nuevos amigos y fortalecer la comunidad universitaria.",
        'event_date' => "2024-07-18",
        'event_time' => "10:00 AM",
        'location' => "Campus Central",
        'price' => "Gratis",
        'category' => "social",
        'image_url' => "https://picsum.photos/seed/integration-day/600/400.jpg"
    ]
];

// Formateamos las fechas para que sean más fáciles de usar en JavaScript
foreach ($events_data as &$event) {
    $timestamp = strtotime($event['event_date']);
    $event['date'] = [
        'day' => date('d', $timestamp),
        'month' => date('M', $timestamp),
        'full' => date('d \d\e F \d\e Y', $timestamp) // Ej: "15 de Junio de 2024"
    ];
    // Renombramos 'image_url' a 'image' para que coincida con el JS
    $event['image'] = $event['image_url'];
}
unset($event); // Rompemos la referencia

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVENTOS UCOMPENSAR</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Enlace a nuestro archivo CSS externo -->
    <link rel="stylesheet" href="../../../public/css/eventos.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EVENTOS UCOMPENSAR</h1>
            <p>Descubre los próximos eventos en nuestra universidad</p>
        </div>
        
        <div class="filter-container">
            <button class="filter-btn active" data-filter="all">Todos</button>
            <button class="filter-btn" data-filter="academic">Académicos</button>
            <button class="filter-btn" data-filter="cultural">Culturales</button>
            <button class="filter-btn" data-filter="sports">Deportivos</button>
            <button class="filter-btn" data-filter="social">Sociales</button>
        </div>
        
        <div class="events-container">
            <div class="events-carousel">
                <div class="events-wrapper" id="eventsWrapper">
                    <!-- Las tarjetas de eventos se generarán dinámicamente con JS -->
                </div>
            </div>
            
            <div class="carousel-nav">
                <button class="carousel-btn" id="prevBtn">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="carousel-btn" id="nextBtn">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            
            <div class="carousel-indicators" id="indicators">
                <!-- Los indicadores se generarán dinámicamente -->
            </div>
        </div>

        <div class="event-details-container" id="eventDetailsContainer">
            <div class="event-details-image" id="detailsImage"></div>
            <div class="event-details-content">
                <h2 id="detailsTitle">Selecciona un evento para ver los detalles</h2>
                <p id="detailsDescription">Navega por el carrusel de arriba y haz clic en cualquier evento para ver su información completa aquí.</p>
                <div class="event-details-meta">
                    <span><i class="bi bi-calendar-event"></i> <span id="detailsDate">--</span></span>
                    <span><i class="bi bi-clock"></i> <span id="detailsTime">--</span></span>
                    <span><i class="bi bi-geo-alt"></i> <span id="detailsLocation">--</span></span>
                </div>
                <button class="event-details-btn" id="detailsRegisterBtn" style="display: none;">Inscribirse Ahora</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // --- PASO DE DATOS DE PHP A JAVASCRIPT ---
            // La variable PHP $events_data se convierte a JSON y se imprime directamente en el código JS.
            const events = <?php echo json_encode($events_data); ?>;
            
            let currentIndex = 0;
            let filteredEvents = [...events];
            const eventsWrapper = $('#eventsWrapper');
            const indicatorsContainer = $('#indicators');
            const detailsContainer = $('#eventDetailsContainer');
            
            // ... (El resto del código JavaScript es exactamente el mismo que antes) ...
            function renderEvents() {
                eventsWrapper.empty();
                indicatorsContainer.empty();
                
                filteredEvents.forEach((event, index) => {
                    const priceClass = event.price === "Gratis" ? "free" : "";
                    
                    const eventCard = `
                        <div class="event-card ${index === currentIndex ? 'active' : ''}" data-category="${event.category}" data-index="${index}">
                            <div class="event-image" style="background-image: url('${event.image}')">
                                <div class="event-date">
                                    <div class="day">${event.date.day}</div>
                                    <div class="month">${event.date.month}</div>
                                </div>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title">${event.title}</h3>
                                <p class="event-description">${event.description.substring(0, 80)}...</p>
                                <div class="event-details">
                                    <span><i class="bi bi-clock"></i> ${event.time}</span>
                                    <span><i class="bi bi-geo-alt"></i> ${event.location}</span>
                                </div>
                                <div class="event-footer">
                                    <div class="event-price ${priceClass}">${event.price}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    eventsWrapper.append(eventCard);
                    const indicator = `<div class="indicator ${index === currentIndex ? 'active' : ''}" data-index="${index}"></div>`;
                    indicatorsContainer.append(indicator);
                });
                
                updateCarouselPosition();
                updateEventDetails(currentIndex);
            }
            
            function updateEventDetails(index) {
                const event = filteredEvents[index];
                if (!event) return;

                detailsContainer.fadeOut(250, function() {
                    $('#detailsImage').css('background-image', `url('${event.image}')`);
                    $('#detailsTitle').text(event.title);
                    $('#detailsDescription').text(event.description);
                    $('#detailsDate').text(event.date.full);
                    $('#detailsTime').text(event.time);
                    $('#detailsLocation').text(event.location);
                    $('#detailsRegisterBtn').data('id', event.id).show();
                    detailsContainer.fadeIn(250);
                });
            }
            
            function updateCarouselPosition() {
                const cardWidth = $('.event-card').outerWidth(true);
                const offset = -currentIndex * cardWidth;
                eventsWrapper.css('transform', `translateX(${offset}px)`);
                
                $('.event-card').removeClass('active');
                $('.event-card').eq(currentIndex).addClass('active');
                
                $('.indicator').removeClass('active');
                $('.indicator').eq(currentIndex).addClass('active');
            }
            
            function moveCarousel(direction) {
                if (direction === 'next') {
                    currentIndex = (currentIndex + 1) % filteredEvents.length;
                } else {
                    currentIndex = (currentIndex - 1 + filteredEvents.length) % filteredEvents.length;
                }
                updateCarouselPosition();
                updateEventDetails(currentIndex);
            }
            
            $('#prevBtn').click(() => moveCarousel('prev'));
            $('#nextBtn').click(() => moveCarousel('next'));
            
            $(document).on('click', '.indicator', function() {
                currentIndex = parseInt($(this).data('index'));
                updateCarouselPosition();
                updateEventDetails(currentIndex);
            });

            $(document).on('click', '.event-card', function() {
                currentIndex = parseInt($(this).data('index'));
                updateCarouselPosition();
                updateEventDetails(currentIndex);
            });
            
            $('.filter-btn').click(function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                const filter = $(this).data('filter');
                
                if (filter === 'all') {
                    filteredEvents = [...events];
                } else {
                    filteredEvents = events.filter(event => event.category === filter);
                }
                
                currentIndex = 0;
                renderEvents();
            });
            
            $('#detailsRegisterBtn').click(function() {
                const eventId = $(this).data('id');
                const event = events.find(e => e.id == eventId);
                
                Swal.fire({
                    title: 'Inscripción a evento',
                    html: `<p>Te has inscrito exitosamente en:</p><h3>${event.title}</h3><p>Fecha: ${event.date.full}</p><p>Hora: ${event.time}</p><p>Lugar: ${event.location}</p>`,
                    type: 'success',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3498db'
                });
            });
            
            renderEvents();
            
            let autoRotateInterval;
            function startAutoRotate() {
                autoRotateInterval = setInterval(() => {
                    moveCarousel('next');
                }, 5000);
            }
            function stopAutoRotate() {
                clearInterval(autoRotateInterval);
            }
            startAutoRotate();
            $('.events-carousel, .event-details-container').hover(stopAutoRotate, startAutoRotate);
            
            let touchStartX = 0;
            let touchEndX = 0;
            $('.events-carousel').on('touchstart', function(e) {
                touchStartX = e.originalEvent.touches[0].clientX;
            });
            $('.events-carousel').on('touchend', function(e) {
                touchEndX = e.originalEvent.changedTouches[0].clientX;
                handleSwipe();
            });
            function handleSwipe() {
                if (touchEndX < touchStartX - 50) {
                    moveCarousel('next');
                }
                if (touchEndX > touchStartX + 50) {
                    moveCarousel('prev');
                }
            }
        });
    </script>
</body>
</html>