$(document).ready(function() {
  $("#formLogin").on("submit", function(e) {
    e.preventDefault();

    $.ajax({
      url: "/src/Pages/Sesion/validar_login.php",  // ruta correcta al PHP
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function(resp) {
        if (resp.status === "ok") {
          Swal.fire({
            icon: "success",
            title: "¡Bienvenido!",
            text: resp.msg,
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.href = "/index.html"; // redirige al home
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: resp.msg
          });
        }
      },
      error: function(xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "No se pudo conectar con el servidor: " + error
        });
      }
    });
  });
});
