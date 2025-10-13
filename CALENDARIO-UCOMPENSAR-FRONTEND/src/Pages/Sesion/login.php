<?php
session_start();
include("../../config/conexion.php");

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (empty($correo) || empty($clave)) {
        $mensaje = "Por favor completa todos los campos.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($clave, $user['clave'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol'];
                header("Location: /index.php");
                exit;
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Usuario no encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>

  <!-- Librerías -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" href="/public/css/login.css">
</head>

<body>
  <!-- MENSAJE DE ERROR -->
  <?php if($mensaje): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?= htmlspecialchars($mensaje) ?>',
        confirmButtonColor: '#f47c20'
      });
    </script>
  <?php endif; ?>

  <div class="fondo"></div>
  <div class="atras">
    <a href="/index.php">
      <i class="bi bi-arrow-left-circle-fill"></i>
    </a>
  </div>

  <div class="row">
    <!-- 🐨 KOALA -->
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

    <!-- 🧠 LOGIN -->
    <div class="col-md-6 registro">
      <h2>Login</h2>
      <form method="POST">
        <input type="email" name="correo" placeholder="Correo" required>

        <!-- Campo de clave con botón de mostrar/ocultar -->
        <div class="password-wrapper">
          <input type="password" name="clave" placeholder="Clave" id="password" required>
          <i id="togglePassword" class="fa fa-eye"></i>
        </div>

        <button type="submit">Login</button>
      </form>

      <div class="register-line">
        <span>¿No tienes cuenta?</span>
        <a href="/src/Pages/Sesion/register.php">Registrarse</a>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="/public/js/animacion-login.js"></script>
  <script>
    // Mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    togglePassword.addEventListener('click', function () {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>
