@echo off
echo Creando estructura del proyecto...
mkdir eventos_universidad
mkdir eventos_universidad\admin
mkdir eventos_universidad\assets
mkdir eventos_universidad\assets\css
mkdir eventos_universidad\assets\js
mkdir eventos_universidad\assets\uploads
mkdir eventos_universidad\student

echo Creando archivos en la raiz...
(
echo ^<?php
echo // db_connect.php
echo $serverName = "localhost"^; // o el nombre de tu servidor SQL, ej: "SQLEXPRESS"
echo $connectionInfo = array^(
echo     "Database" =^> "GestionEventosUniversidad"^,
echo     "UID" =^> "tu_usuario_sql"^, // Ej: "sa"
echo     "PWD" =^> "tu_contraseña_sql" // Tu contrase~a de SQL Server
echo ^)^;
echo.
echo $conn = sqlsrv_connect^($serverName, $connectionInfo^)^;
echo.
echo if ^($conn === false^) ^{
echo     die^("Error al conectar a la base de datos. Revisa las credenciales y que los drivers de SQL Server para PHP est~n instalados. ^<br^>^<pre^>" ^. print_r^(sqlsrv_errors^(^), true^) ^. "^</pre^>"^)^;
echo ^}
echo ?^>
) > "eventos_universidad\db_connect.php"

(
echo ^<?php
echo // functions.php
echo.
echo function obtenerTodosLosEventos^($conn^) ^{
echo     $sql = "SELECT * FROM Eventos ORDER BY fecha_evento ASC"^;
echo     $stmt = sqlsrv_query^($conn, $sql^)^;
echo     $eventos = []^;
echo     while ^($row = sqlsrv_fetch_array^($stmt, SQLSRV_FETCH_ASSOC^)^) ^{
echo         $eventos[] = $row^;
echo     ^}
echo     return $eventos^;
echo ^}
echo.
echo function obtenerEventosPorMes^($conn, $mes, $anio^) ^{
echo     $sql = "SELECT * FROM Eventos WHERE MONTH^(fecha_evento^) = ? AND YEAR^(fecha_evento^) = ? ORDER BY fecha_evento ASC"^;
echo     $params = array^($mes, $anio^)^;
echo     $stmt = sqlsrv_query^($conn, $sql, $params^)^;
echo     $eventos = []^;
echo     while ^($row = sqlsrv_fetch_array^($stmt, SQLSRV_FETCH_ASSOC^)^) ^{
echo         $eventos[] = $row^;
echo     ^}
echo     return $eventos^;
echo ^}
echo.
echo function obtenerEtiquetasDeEvento^($conn, $id_evento^) ^{
echo     $sql = "SELECT e.nombre FROM Etiquetas e
echo             JOIN Evento_Etiqueta ee ON e.id = ee.id_etiqueta
echo             WHERE ee.id_evento = ?"^;
echo     $params = array^($id_evento^)^;
echo     $stmt = sqlsrv_query^($conn, $sql, $params^)^;
echo     $etiquetas = []^;
echo     while ^($row = sqlsrv_fetch_array^($stmt, SQLSRV_FETCH_ASSOC^)^) ^{
echo         $etiquetas[] = $row^;
echo     ^}
echo     return $etiquetas^;
echo ^}
echo.
echo function obtenerTodasLasEtiquetas^($conn^) ^{
echo     $sql = "SELECT * FROM Etiquetas"^;
echo     $stmt = sqlsrv_query^($conn, $sql^)^;
echo     $etiquetas = []^;
echo     while ^($row = sqlsrv_fetch_array^($stmt, SQLSRV_FETCH_ASSOC^)^) ^{
echo         $etiquetas[] = $row^;
echo     ^}
echo     return $etiquetas^;
echo ^}
echo ?^>
) > "eventos_universidad\functions.php"

(
echo ^<?php
echo if ^($_SERVER['REQUEST_METHOD'] === 'POST'^) ^{
echo     require_once 'db_connect.php'^;
echo     $nombre = $_POST['nombre']^;
echo     $email = $_POST['email']^;
echo     $password_hash = hash^('sha256', $_POST['password']^)^;
echo     $sql = "INSERT INTO Estudiantes ^(nombre, email, password_hash^) VALUES ^(?, ?, ?)"^;
echo     $params = array^($nombre, $email, $password_hash^)^;
echo     $stmt = sqlsrv_query^($conn, $sql, $params^)^;
echo     if ^($stmt^) ^{
echo         header^("Location: index.php?registro=exitoso"^)^;
echo     ^} else ^{
echo         die^("Error al registrar. " ^. print_r^(sqlsrv_errors^(^), true^)^)^;
echo     ^}
echo ^}
echo ?^>
) > "eventos_universidad\registrar_estudiante.php"

echo Creando archivos de admin...
(
echo ^<?php
echo session_start^(^)^;
echo require_once '../db_connect.php'^;
echo require_once '../functions.php'^;
echo.
echo $mensaje = ''^;
echo $eventoEditar = null^;
echo.
echo if ^(isset^($_GET['eliminar']^)^) ^{
echo     $id = ^(int^)$_GET['eliminar']^;
echo     $sql = "DELETE FROM Eventos WHERE id = ?"^;
echo     $params = array^($id^)^;
echo     sqlsrv_query^($conn, $sql, $params^)^;
echo     $mensaje = "Evento eliminado."^;
echo ^}
echo.
echo if ^(isset^($_GET['editar']^)^) ^{
echo     $id = ^(int^)$_GET['editar']^;
echo     $sql = "SELECT * FROM Eventos WHERE id = ?"^;
echo     $stmt = sqlsrv_query^($conn, $sql, array^($id^)^)^;
echo     $eventoEditar = sqlsrv_fetch_array^($stmt, SQLSRV_FETCH_ASSOC^)^;
echo ^}
echo.
echo $eventos = obtenerTodosLosEventos^($conn^)^;
echo $etiquetasDisponibles = obtenerTodasLasEtiquetas^($conn^)^;
echo ?^>
echo ^<!DOCTYPE html^>
echo ^<html lang="es"^>
echo ^<head^>
echo     ^<meta charset="UTF-8"^>
echo     ^<title^>Panel de Administraci~n^</title^>
echo     ^<link rel="stylesheet" href="../assets/css/style.css"^>
echo ^</head^>
echo ^<body^>
echo     ^<header^>
echo         ^<h1^>Panel de Administraci~n^</h1^>
echo         ^<nav^>^<a href="../index.php"^>Volver al Inicio^</a^>^</nav^>
echo     ^</header^>
echo     ^<main class="admin-main"^>
echo         ^<?php if ^($mensaje^): ?^>
echo             ^<p style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px;"^>^<?php echo $mensaje; ?^>^</p^>
echo         ^<?php endif; ?^>
echo         ^<section class="admin-form"^>
echo             ^<h2^>^<?php echo $eventoEditar ? 'Editar Evento' : 'Crear Nuevo Evento'; ?^>^</h2^>
echo             ^<form action="procesar_evento.php" method="POST" enctype="multipart/form-data"^>
echo                 ^<input type="hidden" name="id_evento" value="^<?php echo $eventoEditar['id'] ?? ''; ?^>"^>
echo                 ^<label for="nombre"^>Nombre del Evento:^</label^>
echo                 ^<input type="text" id="nombre" name="nombre" value="^<?php echo htmlspecialchars^($eventoEditar['nombre'] ?? ''^); ?^>" required^>
echo                 ^<label for="fecha_evento"^>Fecha del Evento:^</label^>
echo                 ^<input type="date" id="fecha_evento" name="fecha_evento" value="^<?php echo $eventoEditar ? date^('Y-m-d', strtotime^($eventoEditar['fecha_evento']^)^) : ''; ?^>" required^>
echo                 ^<label for="descripcion"^>Descripci~n:^</label^>
echo                 ^<textarea id="descripcion" name="descripcion" rows="5" required^>^<?php echo htmlspecialchars^($eventoEditar['descripcion'] ?? ''^); ?^>^</textarea^>
echo                 ^<button type="submit" class="btn btn-primary"^>^<?php echo $eventoEditar ? 'Actualizar' : 'Crear'; ?^>^</button^>
echo             ^</form^>
echo         ^</section^>
echo     ^</main^>
echo ^</body^>
echo ^</html^>
) > "eventos_universidad\admin\index.php"

(
echo ^<?php
echo require_once '../db_connect.php'^;
echo.
echo if ^($_SERVER['REQUEST_METHOD'] === 'POST'^) ^{
echo     $id_evento = isset^($_POST['id_evento']^) ? ^(int^)$_POST['id_evento'] : null^;
echo     $nombre = $_POST['nombre']^;
echo     $fecha_evento = $_POST['fecha_evento']^;
echo     $descripcion = $_POST['descripcion']^;
echo     $imagen_url = 'placeholder.png'^; // Simplificado para el script
echo.
echo     if ^($id_evento^) ^{
echo         $sql = "UPDATE Eventos SET nombre = ?, descripcion = ?, fecha_evento = ?, imagen_url = ? WHERE id = ?"^;
echo         $params = array^($nombre, $descripcion, $fecha_evento, $imagen_url, $id_evento^)^;
echo     ^} else ^{
echo         $sql = "INSERT INTO Eventos ^(nombre, descripcion, fecha_evento, imagen_url, id_creador^) VALUES ^(?, ?, ?, ?, 1^)"^;
echo         $params = array^($nombre, $descripcion, $fecha_evento, $imagen_url^)^;
echo     ^}
echo     $stmt = sqlsrv_query^($conn, $sql, $params^)^;
echo     header^("Location: index.php?status=success"^)^;
echo ^}
echo ?^>
) > "eventos_universidad\admin\procesar_evento.php"

echo Creando archivos de student...
(
echo ^<?php
echo session_start^(^)^;
echo $_SESSION['estudiante_id'] = 1^; 
echo require_once '../db_connect.php'^;
echo require_once '../functions.php'^;
echo.
echo $estudiante_id = $_SESSION['estudiante_id']^;
echo $eventosFiltrados = obtenerTodosLosEventos^($conn^)^;
echo $etiquetasDisponibles = obtenerTodasLasEtiquetas^($conn^)^;
echo ?^>
echo ^<!DOCTYPE html^>
echo ^<html lang="es"^>
echo ^<head^>
echo     ^<meta charset="UTF-8"^>
echo     ^<title^>Portal del Estudiante^</title^>
echo     ^<link rel="stylesheet" href="../assets/css/style.css"^>
echo ^</head^>
echo ^<body^>
echo     ^<header^>
echo         ^<h1^>Mi Portal de Eventos^</h1^>
echo         ^<nav^>^<a href="../index.php"^>Volver al Inicio^</a^>^</nav^>
echo     ^</header^>
echo     ^<main^>
echo         ^<section class="events-feed"^>
echo             ^<h2^>Eventos Recomendados para Ti^</h2^>
echo             ^<?php if ^(count^($eventosFiltrados^) ^> 0^): ?^>
echo                 ^<?php foreach ^($eventosFiltrados as $evento^): ?^>
echo                     ^<article class="event-card"^>
echo                         ^<img src="../assets/uploads/^<?php echo htmlspecialchars^($evento['imagen_url'] ?? 'placeholder.png'^); ?^>" alt="^<?php echo htmlspecialchars^($evento['nombre']^); ?^>"^>
echo                         ^<div class="event-content"^>
echo                             ^<h3^>^<?php echo htmlspecialchars^($evento['nombre']^); ?^>^</h3^>
echo                             ^<p class="event-date"^>Fecha: ^<?php echo date^('d/m/Y', strtotime^($evento['fecha_evento']^)^); ?^>^</p^>
echo                             ^<p^>^<?php echo nl2br^(htmlspecialchars^($evento['descripcion']^)^); ?^>^</p^>
echo                         ^</div^>
echo                     ^</article^>
echo                 ^<?php endforeach; ?^>
echo             ^<?php else: ?^>
echo                 ^<p^>No hay eventos que coincidan con tus intereses.^</p^>
echo             ^<?php endif; ?^>
echo         ^</section^>
echo     ^</main^>
echo ^</body^>
echo ^</html^>
) > "eventos_universidad\student\index.php"

echo Creando archivos de assets...
(
echo /* Estilos generales */
echo body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f4; }
echo header { background-color: #003366; color: white; padding: 1rem; text-align: center; }
echo header nav a { color: white; margin: 0 15px; text-decoration: none; font-size: 1.1rem; }
echo main { padding: 20px; max-width: 1200px; margin: auto; }
echo /* Feed de Eventos */
echo .events-feed h2 { border-bottom: 2px solid #003366; padding-bottom: 10px; }
echo .event-card { display: flex; background: white; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; overflow: hidden; }
echo .event-card img { width: 200px; height: 150px; object-fit: cover; }
echo .event-content { padding: 15px; flex-grow: 1; }
echo .event-content h3 { margin-top: 0; }
echo .event-date { font-size: 0.9em; color: #555; font-style: italic; }
echo /* Formularios */
echo .admin-form { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
echo .admin-form input, .admin-form textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
echo .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; color: white; text-decoration: none; display: inline-block; }
echo .btn-primary { background: #007bff; }
echo .btn-danger { background: #dc3545; }
echo .btn-warning { background: #ffc107; color: #333; }
echo .event-list-table { width: 100%; border-collapse: collapse; }
echo .event-list-table th, .event-list-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
echo .event-list-table th { background-color: #f2f2f2; }
) > "eventos_universidad\assets\css\style.css"

(
echo document.addEventListener^('DOMContentLoaded', function^(^) ^{
echo     const btnAnterior = document.getElementById^('btnAnterior'^)^;
echo     const btnSiguiente = document.getElementById^('btnSiguiente'^)^;
echo     if^(btnAnterior^) btnAnterior.addEventListener^('click', ^(^) =^> window.location.href = '?mes=' + ^(new URLSearchParams^(window.location.search^).get^('mes'^) || new Date^(^).getMonth^(^)^+1^) -1^)^;
echo     if^(btnSiguiente^) btnSiguiente.addEventListener^('click', ^(^) =^> window.location.href = '?mes=' + ^(new URLSearchParams^(window.location.search^).get^('mes'^) || new Date^(^).getMonth^(^)^+1^) +1^)^;
echo ^}^)^;
) > "eventos_universidad\assets\js\main.js"

echo.
echo Proyecto 'eventos_universidad' creado con exito.
pause
