document.addEventListener('DOMContentLoaded', () => {
    
    const logos = document.querySelectorAll('.header-logo');
    
    logos.forEach(logo => {
        logo.style.cursor = 'pointer'; 
        logo.title = "Un secret s'y cache...";
        
        logo.addEventListener('click', (e) => {
            e.preventDefault();
            if (!document.getElementById('jeu-container')) {
                lancerLaPluieDeCroquettes();
            }
        });
    });
});

function lancerLaPluieDeCroquettes() {
    // --- CRÉATION DE L'INTERFACE DU JEU ---
    const container = document.createElement('div');
    container.id = 'jeu-container';
    container.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0, 0, 0, 0.85); z-index: 9999;
        display: flex; flex-direction: column; align-items: center;
        color: white; font-family: sans-serif; overflow: hidden;
    `;

    const hud = document.createElement('div');
    hud.style.cssText = "position: absolute; top: 20px; font-size: 24px; font-weight: bold; text-align: center;";
    hud.innerHTML = `Score: <span id="jeu-score">0</span><br><small style="font-size:14px; font-weight:normal;">Évitez le chocolat (🍫) !</small>`;
    
    const zoneJeu = document.createElement('div');
    zoneJeu.id = "zone-jeu";
    zoneJeu.style.cssText = "position: relative; width: 100%; max-width: 600px; height: 100%; border-left: 2px solid #555; border-right: 2px solid #555;";

    const bulldog = document.createElement('div');
    bulldog.id = "joueur-bulldog";
    bulldog.innerHTML = "🐶";
    bulldog.style.cssText = "position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); font-size: 50px; user-select: none;";

    const btnFermer = document.createElement('button');
    btnFermer.innerText = "❌ Quitter";
    btnFermer.style.cssText = "position: absolute; top: 20px; right: 20px; padding: 10px; cursor: pointer; border: none; border-radius: 5px; background: #ff4444; color: white;";
    btnFermer.onclick = () => container.remove();

    zoneJeu.appendChild(bulldog);
    container.appendChild(hud);
    container.appendChild(zoneJeu);
    container.appendChild(btnFermer);
    document.body.appendChild(container);

    // --- LOGIQUE DU JEU ---
    let score = 0;
    let jeuActif = true;
    let objets = [];
    let positionJoueur = zoneJeu.offsetWidth / 2;
    const scoreElement = document.getElementById('jeu-score');

    zoneJeu.addEventListener('mousemove', (e) => {
        let rect = zoneJeu.getBoundingClientRect();
        let x = e.clientX - rect.left;
        if (x > 25 && x < rect.width - 25) {
            positionJoueur = x;
            bulldog.style.left = positionJoueur + 'px';
        }
    });
    zoneJeu.addEventListener('touchmove', (e) => {
        let rect = zoneJeu.getBoundingClientRect();
        let x = e.touches[0].clientX - rect.left;
        if (x > 25 && x < rect.width - 25) {
            positionJoueur = x;
            bulldog.style.left = positionJoueur + 'px';
        }
    });

    function creerObjet() {
        if (!jeuActif) return;
        
        const types = [
            { emoji: "🦴", points: 5, type: "bon" },
            { emoji: "🥩", points: 10, type: "bon" },
            { emoji: "🧆", points: 2, type: "bon" },
            { emoji: "🍫", points: 0, type: "mauvais" }
        ];
        
        let choix = types[Math.floor(Math.random() * types.length)];
        let objEl = document.createElement('div');
        objEl.innerHTML = choix.emoji;
        objEl.style.cssText = `position: absolute; top: -30px; font-size: 30px; left: ${Math.random() * 90}%;`;
        zoneJeu.appendChild(objEl);
        
        objets.push({ el: objEl, y: -30, data: choix });
        
        let delai = Math.max(300, 1000 - (score * 5)); 
        setTimeout(creerObjet, delai);
    }

    // Boucle d'animation principale
    function update() {
        if (!jeuActif) return;

        let rectChien = bulldog.getBoundingClientRect();

        for (let i = 0; i < objets.length; i++) {
            let obj = objets[i];
            obj.y += 4 + (score / 20);
            obj.el.style.top = obj.y + 'px';

            let rectObj = obj.el.getBoundingClientRect();

            let collisionX = (rectObj.left < rectChien.right - 15) && (rectObj.right > rectChien.left + 15);
            let collisionY = (rectObj.bottom > rectChien.top + 10) && (rectObj.top < rectChien.bottom);

            if (collisionX && collisionY) {
                if (obj.data.type === "mauvais") {
                    // Perdu !
                    jeuActif = false;
                    hud.innerHTML = `<span style="color:#ff4444">Aïe ! Le chocolat est toxique !</span><br>Score final : ${score}`;
                    bulldog.innerHTML = "😵";
                } else {
                    score += obj.data.points;
                    scoreElement.innerText = score;
                    
                    bulldog.innerHTML = "😋";
                    setTimeout(() => { if(jeuActif) bulldog.innerHTML = "🐶"; }, 200);
                }
                
                obj.el.remove();
                objets.splice(i, 1);
                i--;
            } 
            else if (obj.y > zoneJeu.offsetHeight) {
                obj.el.remove();
                objets.splice(i, 1);
                i--;
            }
        }
        requestAnimationFrame(update);
    }

    setTimeout(creerObjet, 1000);
    requestAnimationFrame(update);
}



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
