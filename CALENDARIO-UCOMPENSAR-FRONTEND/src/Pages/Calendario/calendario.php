<?php
// Inicia sesión para manejar el menú dinámico
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Calendario Eventos Ucompensar</title>

  <!-- Estilos principales -->
  <link rel="stylesheet" href="/public/css/calendario.css" />
  <link rel="stylesheet" href="/public/css/estructura.css" />

  <!-- Librerías externas -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- jQuery y Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>

<body>
  <!-- MENU -->
  <?php include(__DIR__ . '/../../Components/menu.php'); ?>
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
  <script src="/public/js/calendario.js"></script>

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
      align-items: flex-start;
      min-height: 100vh;
      overflow-x: hidden;
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
</body>
</html>
