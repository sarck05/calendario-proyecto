document.getElementById("formRegistro").addEventListener("submit", async (e) => {
  e.preventDefault();

  const emailTo = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;

  if (password !== confirmPassword) {
    Swal.fire("Error", "Las contraseñas no coinciden", "error");
    return;
  }

  try {
    const response = await fetch("https://backend-calendario-ucompensar.onrender.com/enviar-email", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        emailTo,
        asunto: "Verificación de registro",
        html: `<h2>Hola 👋</h2><p>Tu registro fue exitoso. Bienvenido a la plataforma.</p>`,
      }),
    });

    const data = await response.json();

    if (data.success) {
      Swal.fire("Registro exitoso", "Se envió un correo de verificación ✅", "success");
    } else {
      Swal.fire("Error", data.error || "No se pudo enviar el correo", "error");
    }
  } catch (err) {
    console.error(err);
    Swal.fire("Error", "No se pudo conectar con el servidor", "error");
  }
});
