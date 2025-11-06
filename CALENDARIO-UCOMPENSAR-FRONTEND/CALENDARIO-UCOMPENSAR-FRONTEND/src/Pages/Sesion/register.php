<?php
session_start();
include("../../config/conexion.php");

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $tipoIdentificacion = $_POST['tipoIdentificacion'] ?? '';
    $numeroIdentificacion = $_POST['numeroIdentificacion'] ?? '';
    $correo = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $confirmClave = $_POST['confirmClave'] ?? '';

    if (empty($nombres) || empty($apellidos) || empty($tipoIdentificacion) || empty($numeroIdentificacion) || empty($correo) || empty($clave) || empty($confirmClave)) {
        $mensaje = "Por favor completa todos los campos.";
    } elseif ($clave !== $confirmClave) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el correo ya existe
        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
        } else {
            $hashClave = password_hash($clave, PASSWORD_DEFAULT);
            $sqlInsert = "INSERT INTO usuarios (nombre, correo, clave, rol) VALUES (?, ?, ?, 'estudiante')";
            $stmtInsert = $conn->prepare($sqlInsert);
            $nombreCompleto = $nombres . ' ' . $apellidos;
            $stmtInsert->bind_param("sss", $nombreCompleto, $correo, $hashClave);
            if ($stmtInsert->execute()) {
                $mensaje = "Registro exitoso. Ya puedes iniciar sesión.";
            } else {
                $mensaje = "Error al registrar. Intenta nuevamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
  <link rel="stylesheet" href="/public/css/register.css">
</head>
<body>
  <!-- MENSAJE DE ALERT -->
  <?php if($mensaje): ?>
    <script>
      Swal.fire({
        icon: 'info',
        title: 'Aviso',
        text: '<?= htmlspecialchars($mensaje) ?>',
        confirmButtonColor: '#f47c20'
      });
    </script>
  <?php endif; ?>

  <div class="fondo"></div>
  <div class="atras"><a href="/index.php"><i class="bi bi-arrow-left-circle-fill"></i></a></div>

  <div class="row">
    <!-- KOALA -->
    <div class="col-md-6 mascota">
      <div class="koala">
        <div class="ear left-ear"></div>
        <div class="ear right-ear"></div>
        <div class="head">
          <div class="eye left-eye"></div>
          <div class="eye right-eye"></div>
          <div class="glasses"></div>
          <div class="nose"></div>
          <div class="mouth"></div>
        </div>
        <div class="body">
          <div class="hand left-hand"></div>
          <div class="hand right-hand"></div>
        </div>
      </div>
    </div>

    <!-- REGISTRO -->
    <div class="col-md-6 registro">
      <h2>Registro</h2>
      <form method="POST">
        <div class="form-row">
          <input type="text" name="nombres" placeholder="Nombres" required>
          <input type="text" name="apellidos" placeholder="Apellidos" required>
        </div>
        <div class="form-row">
          <select name="tipoIdentificacion" required>
            <option value="">Tipo de identificación</option>
            <option value="cc">CC</option>
            <option value="ce">CE</option>
            <option value="ti">TI</option>
          </select>
          <input type="text" name="numeroIdentificacion" placeholder="Número de identificación" required>
        </div>
        <input type="email" name="email" placeholder="Correo" required>
        <div class="password-wrapper">
          <input type="password" name="clave" placeholder="Contraseña" id="password" required>
          <i id="togglePassword" class="fa fa-eye"></i>
        </div>
        <div class="password-wrapper">
          <input type="password" name="confirmClave" placeholder="Confirmar Contraseña" id="confirmPassword" required>
          <i id="toggleConfirmPassword" class="fa fa-eye"></i>
        </div>
        <button type="submit">Registrarse</button>
      </form>

      <div class="login-line">
        <span>¿Ya tienes cuenta?</span> <a href="/src/Pages/Sesion/login.php">Login</a>
      </div>
    </div>
  </div>

<script>
  // Mostrar/ocultar contraseña
  const togglePassword = document.getElementById('togglePassword');
  const password = document.getElementById('password');
  togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
  });

  const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
  const confirmPassword = document.getElementById('confirmPassword');
  toggleConfirmPassword.addEventListener('click', function () {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
  });
</script>
<script src="/public/js/animacion-registro.js"></script>
</body>
</html>
