function setCookie(cookieName, cookieValue, expiration = null) {
  if (expiration == null)
    expiration = new Date(Date.now() + 86400000).toUTCString();
  document.cookie =
    cookieName + "=" + cookieValue + "; expires=" + expiration + "; path=/;";
}

function getCookie(cookieName, defaultValue = null) {
  const cookies = document.cookie.split(";");
  let row = cookies.find((row) => row.trim().startsWith(cookieName + "="));
  if (row == null) return defaultValue;
  return row.split("=")[1];
}

let cookieTheme = getCookie("theme");

if (cookieTheme == "dark") {
  document
    .getElementById("theme-css")
    .setAttribute("href", "css/variables-sombres.css");
  document.getElementById("btn-theme").innerHTML = "☀️";
} else if (cookieTheme == "clair" || cookieTheme == null) {
  document
    .getElementById("theme-css")
    .setAttribute("href", "css/variables.css");
  document.getElementById("btn-theme").innerHTML = "🌙";
} else {
  document
    .getElementById("theme-css")
    .setAttribute("href", "css/variables.css");
  document.getElementById("btn-theme").innerHTML = "🌙";
}

function changerTheme() {
  let cookieTheme = getCookie("theme");

  if (cookieTheme == "dark") {
    document
      .getElementById("theme-css")
      .setAttribute("href", "css/variables.css");
    document.getElementById("btn-theme").innerHTML = "🌙";
    setCookie("theme", "clair");
  } else {
    document
      .getElementById("theme-css")
      .setAttribute("href", "css/variables-sombres.css");
    document.getElementById("btn-theme").innerHTML = "☀️";
    setCookie("theme", "dark");
  }
}

// Vérification du statut de blocage toutes les 5 secondes
if (document.cookie.includes("client=")) {
  // Ne vérifie que si l'utilisateur est connecté
  setInterval(function () {
    fetch("verifier_blocage.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.bloque === true) {
          alert(data.message);
          window.location.href = "index.php";
        }
      })
      .catch((error) =>
        console.error("Erreur lors de la vérification du blocage :", error),
      );
  }, 5000);
}
