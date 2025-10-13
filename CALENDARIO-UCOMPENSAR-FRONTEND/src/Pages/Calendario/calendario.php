<?php
include(__DIR__ . '/../../config/conexion.php');

$sql = "SELECT id, nombre, descripcion, tipo, fecha FROM eventos";
$result = $conn->query($sql);

$eventos = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
  }
}
$conn->close();
?>
<script>
  // 🔹 Pasamos los eventos de PHP a JS
  const eventos = <?php echo json_encode($eventos, JSON_UNESCAPED_UNICODE); ?>;
</script>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Calendario Eventos Ucompensar</title>

  <!-- Estilos principales -->
  <link rel="stylesheet" href="../../../public/css/calendario.css" />
  
  <!-- Librerías externas -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- jQuery y Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>

<body>
<!-- MENU -->
<?php include('../../Components/menu.php'); ?>
<!-- FIN MENU -->

  <main class="calendar-wrapper">
    <header class="calendar-header">
      <button class="nav-button" id="prevButton">&lt;</button>
      <div class="header-nav">
        <h1 id="currentPeriod"></h1>
      </div>
      <button class="nav-button" id="nextButton">&gt;</button>

      <!-- Selector de Vista -->
      <div class="view-selector">
        <button class="view-button active" data-view="month">Mes</button>
        <button class="view-button" data-view="week">Semana</button>
        <button class="view-button" data-view="day">Día</button>
      </div>
    </header>

    <section class="calendar-body">
      <!-- Vista de Mes -->
      <div class="month-view" id="monthView">
        <table>
          <thead>
            <tr>
              <th>Dom</th>
              <th>Lun</th>
              <th>Mar</th>
              <th>Mié</th>
              <th>Jue</th>
              <th>Vie</th>
              <th>Sáb</th>
            </tr>
          </thead>
          <tbody id="calendarDays"></tbody>
        </table>
      </div>

      <!-- Vista de Semana -->
      <div class="week-view" id="weekView">
        <div class="week-grid" id="weekGrid"></div>
      </div>

      <!-- Vista de Día -->
      <div class="day-view" id="dayView">
        <div class="day-date" id="dayDate"></div>
        <div class="day-events" id="dayEvents"></div>
      </div>
    </section>
  </main>

  <!-- JS del calendario -->
  <script src="../../../public/js/calendario.js"></script>
</body>
</html>


<style>
  /* ===== Ajustes globales solo para el calendario ===== */
  body {
    margin: 0;
    background-color: var(--color-bg);
    color: var(--color-text);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding-top: 90px; /* deja espacio para el menú fijo */
    display: flex;
    justify-content: center;
    align-items: flex-start; /* evita que se centre verticalmente */
    min-height: 100vh;
  }

  .menu-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }


  /* --- Control del scroll --- */
  html, body {
    overflow-x: hidden;
  }

 h3 {
  font-family: 'Cormorant Garamond', serif;
  font-weight: 700;
  color:black;
  margin: 0 0 5px 0;
}

h1 {
  font-family: 'Cormorant Garamond', serif;
  color: #fff;
  margin: 0 0 5px 0;
}

p {
  font-family: 'Lora', serif;
  font-weight: 800;
  font-size: 2rem;
  color: #2d6a4f;
}

</style>
