<?php
session_start(); // Para manejar el menú dinámico según sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Contáctanos - UCompensar</title>

  <!-- Librerías externas -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="/public/css/estructura.css">
  <style>
    body {
      background-color: #f5f8fa;
    }
    .form-container {
      max-width: 600px;
      margin: 60px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .form-container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }
    .btn-enviar {
      background-color: #f47c20;
      color: white;
      border: none;
    }
    .btn-enviar:hover {
      background-color: #d8661a;
    }
  </style>
</head>
<body>

  <!-- MENU -->
  <?php include(__DIR__ . '/../../Components/menu.php'); ?>
  <!-- FIN MENU -->

  <div class="form-container">
    <h2><i class="bi bi-chat-dots-fill"></i> Contáctanos</h2>
    <form action="https://formspree.io/f/manpdklb" method="POST" id="contactForm">
      <div class="form-group">
        <label for="nombre"><i class="bi bi-person-fill"></i> Nombre completo</label>
        <input type="text" class="form-control" name="name" id="nombre" required placeholder="Tu nombre">
      </div>
      <div class="form-group">
        <label for="email"><i class="bi bi-envelope-fill"></i> Correo electrónico</label>
        <input type="email" class="form-control" name="email" id="email" required placeholder="ejemplo@correo.com">
      </div>
      <div class="form-group">
        <label for="asunto"><i class="bi bi-pencil-fill"></i> Asunto</label>
        <input type="text" class="form-control" name="subject" id="asunto" required placeholder="¿Sobre qué quieres hablar?">
      </div>
      <div class="form-group">
        <label for="mensaje"><i class="bi bi-chat-left-text-fill"></i> Mensaje</label>
        <textarea class="form-control" name="message" id="mensaje" rows="5" required placeholder="Escribe tu mensaje aquí..."></textarea>
      </div>
      <button type="submit" class="btn btn-enviar btn-block">Enviar mensaje</button>
    </form>
  </div>

  <script>
    $('#contactForm').on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function() {
          Swal.fire({
            icon: 'success',
            title: '¡Mensaje enviado!',
            text: 'Gracias por contactarnos. Te responderemos pronto.',
            confirmButtonColor: '#f47c20'
          });
          $('#contactForm')[0].reset();
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al enviar tu mensaje. Intenta nuevamente.',
            confirmButtonColor: '#f47c20'
          });
        }
      });
    });
  </script>

</body>
</html>
