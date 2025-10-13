<?php
session_start();
$logeado = isset($_SESSION['usuario_id']);
$nombre = $logeado ? $_SESSION['usuario_nombre'] : '';
$rol = $logeado ? $_SESSION['usuario_rol'] : '';
?>
<!-- COMPONENTE: MENÚ PRINCIPAL -->
<link rel="stylesheet" href="/public/css/estructura.css">
<div class="container-fluid menu-container">
  <div class="row menu-row">
    <div class="col-md-3 menu-logo">
      <img src="/public/img/logo-compensar.png" alt="Logo Compensar" class="img-responsive">
    </div>
    <div class="col-md-6">
      <ul class="nav navbar-nav menu-principal">
        <li><a href="/index.php">Inicio</a></li>
        <li><a href="/src/Pages/Calendario/calendario.php">Calendario</a></li>
        <li><a href="/src/Pages/Calendario/eventos.php">Eventos</a></li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Facultades <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/src/Pages/Facultades/Ingenieria.php">Ingeniería</a></li>
            <li><a href="/src/Pages/Facultades/CienciasSociales.php">Ciencias Sociales</a></li>
            <li><a href="/src/Pages/Facultades/Artes.php">Artes</a></li>
            <li><a href="/src/Pages/Facultades/Medicina.php">Medicina</a></li>
          </ul>
        </li>
        <li><a href="/src/Pages/Calendario/noticias.php">Noticias</a></li>
        <li><a href="/src/Pages/Sesion/Contacto.php">Contáctanos</a></li>
      </ul>
    </div>
    <div class="col-md-3" style="margin-right: 20px;">
      <ul class="nav navbar-nav navbar-right menu-sesion">
        <?php if (!$logeado): ?>
          <!-- Usuario no logueado -->
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="bi bi-person-fill-down"></i> Iniciar Sesión <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="/src/Pages/Sesion/login.php"><i class="bi bi-person-fill"></i> Login</a></li>
              <li><a href="/src/Pages/Sesion/register.php"><i class="bi bi-person-fill-add"></i> Registrarse</a></li>
            </ul>
          </li>
        <?php else: ?>
          <!-- Usuario logueado -->
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="bi bi-person-fill"></i> <?= htmlspecialchars($nombre) ?> <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <?php if($rol === 'admin'): ?>
                <li><a href="/src/Pages/Panel/panel.php"><i class="bi bi-speedometer2"></i> Panel Central</a></li>
              <?php endif; ?>
              <li><a href="/src/Pages/Sesion/perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
              <li><a href="/src/Pages/Sesion/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>
