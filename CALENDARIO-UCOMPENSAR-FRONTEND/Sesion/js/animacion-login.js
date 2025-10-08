const passwordInput = document.getElementById("password");
const koala = document.querySelector(".koala");
const eyes = document.querySelectorAll(".eye");
const togglePassword = document.getElementById("togglePassword");

// Efecto normal cuando se enfoca el input de contraseña
passwordInput.addEventListener("focus", () => {
  koala.classList.add("cover-eyes");
  eyes.forEach(eye => eye.classList.add("closed"));
});

passwordInput.addEventListener("blur", () => {
  koala.classList.remove("cover-eyes");
  eyes.forEach(eye => eye.classList.remove("closed"));
});

// Mostrar contraseña solo mientras se presiona el botón
togglePassword.addEventListener("mousedown", () => {
  passwordInput.type = "text"; 
  koala.classList.remove("cover-eyes");
  eyes.forEach(eye => eye.classList.remove("closed"));
});

togglePassword.addEventListener("mouseup", () => {
  passwordInput.type = "password"; 
  koala.classList.add("cover-eyes");
  eyes.forEach(eye => eye.classList.add("closed"));
});

togglePassword.addEventListener("mouseleave", () => {
  passwordInput.type = "password";
  koala.classList.add("cover-eyes");
  eyes.forEach(eye => eye.classList.add("closed"));
});

// Mensaje de confirmación al enviar el formulario
document.getElementById("formLogin").addEventListener("submit", (e) => {
  e.preventDefault();
  Swal.fire({
    title: "¡Login exitoso!",
    text: "Bienvenido de nuevo 🐨",
    icon: "success",
    confirmButtonText: "OK"
  });
});
