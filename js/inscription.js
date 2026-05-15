
// limite de caractère
let LIMITE_EMAIL = 50;
let LIMITE_MOT_DE_PASSE = 50;
let LIMITE_PRENOM = 50;
let LIMITE_NOM = 50;
let LIMITE_TEL = 14;


// affiche un message d'erreur
function afficherErreur(champ, message) {
    
    let erreurElement = document.getElementById("erreur_" + champ.id);

    if (message === "") {
        
        champ.classList.remove("champ_erreur");
        if (erreurElement !== null) {
            erreurElement.innerHTML = "";
        }
    } else {
        
        champ.classList.add("champ_erreur");
        if (erreurElement !== null) {
            erreurElement.innerHTML = message;
        }
    }
}


// fct qui met a jour le compteur pour voir si on est bien dans la limite

function mettreAJourCompteur(champ, limite) {
    let compteurElement = document.getElementById("compteur_" + champ.id);
    if (compteurElement !== null) {
        let utilises = champ.value.length;
        let restants = limite - utilises;
        compteurElement.innerHTML = utilises + " / " + limite + " caractères";

        // Si on dépasse la limite, on colorie le compteur en rouge
        if (restants < 0) {
            compteurElement.classList.add("compteur_depasse");
        } else {
            compteurElement.classList.remove("compteur_depasse");
        }
    }
}


// fonctions pour valider chaque champ (case vide ou respect de la limite du champ)
function validerPrenom() {
    let champ = document.getElementById("idname");
    let valeur = champ.value.trim();

    if (valeur === "") {
        afficherErreur(champ, "Le prénom est obligatoire.");
        return false;
    }
    if (valeur.length > LIMITE_PRENOM) {
        afficherErreur(champ, "Le prénom ne doit pas dépasser " + LIMITE_PRENOM + " caractères.");
        return false;
    }
    afficherErreur(champ, "");
    return true;
}

function validerNom() {
    let champ = document.getElementById("idfname");
    let valeur = champ.value.trim();

    if (valeur === "") {
        afficherErreur(champ, "Le nom est obligatoire.");
        return false;
    }
    if (valeur.length > LIMITE_NOM) {
        afficherErreur(champ, "Le nom ne doit pas dépasser " + LIMITE_NOM + " caractères.");
        return false;
    }
    afficherErreur(champ, "");
    return true;
}

function validerAdresse() {
    let champ = document.getElementById("idadr");
    let valeur = champ.value.trim();

    if (valeur === "") {
        afficherErreur(champ, "L'adresse postale est obligatoire.");
        return false;
    }
    afficherErreur(champ, "");
    return true;
}


function validerTelephone() {
    let champ = document.getElementById("idtel");
    let valeur = champ.value.trim();

    if (valeur === "") {
        afficherErreur(champ, "Le numéro de téléphone est obligatoire.");
        return false;
    }

    if (valeur.length < 7 || valeur.length > 15) {
        afficherErreur(champ, "Le numéro doit contenir entre 7 et 15 caractères.");
        return false;
    }

    // On vérifie chaque caractère
    for (let i = 0; i < valeur.length; i++) {
        let car = valeur[i];
        if (car !== " " && car !== "-" && car !== "." && (car < "0" || car > "9")) {
            afficherErreur(champ, "Le numéro ne doit contenir que des chiffres, espaces, tirets ou points.");
            return false;
        }
    }

    afficherErreur(champ, "");
    return true;
}


function validerEmail() {
    let champ = document.getElementById("idemail");
    let valeur = champ.value.trim();

    if (valeur === "") {
        afficherErreur(champ, "L'adresse email est obligatoire.");
        return false;
    }

    if (valeur.length > LIMITE_EMAIL) {
        afficherErreur(champ, "L'email ne doit pas dépasser " + LIMITE_EMAIL + " caractères.");
        return false;
    }

    // verif @
    let parties = valeur.split("@");
    if (parties.length !== 2) {
        afficherErreur(champ, "L'adresse email n'est pas valide (ex: nom@domaine.fr).");
        return false;
    }

    // verif .
    let apresArobase = parties[1];
    if (!apresArobase.includes(".")) {
        afficherErreur(champ, "L'adresse email n'est pas valide (ex: nom@domaine.fr).");
        return false;
    }

    // verifier qu'il y ait des caracteres avant et apres l'arobase et apres le point
    if (parties[0] === "" || apresArobase === "" || apresArobase.endsWith(".")) {
        afficherErreur(champ, "L'adresse email n'est pas valide (ex: nom@domaine.fr).");
        return false;
    }

    afficherErreur(champ, "");
    return true;
}


function validerMotDePasse() {
    let champ = document.getElementById("idcode");
    let valeur = champ.value;

    if (valeur === "") {
        afficherErreur(champ, "Le mot de passe est obligatoire.");
        return false;
    }
    if (valeur.length < 6) { //min 6 caracteres dans le mdp
        afficherErreur(champ, "Le mot de passe doit contenir au moins 6 caractères.");
        return false;
    }
    if (valeur.length > LIMITE_MOT_DE_PASSE) {
        afficherErreur(champ, "Le mot de passe ne doit pas dépasser " + LIMITE_MOT_DE_PASSE + " caractères.");
        return false;
    }
    afficherErreur(champ, "");
    return true;
}


function basculerVisibiliteMotDePasse() {
    let champ = document.getElementById("idcode");

    if (champ.getAttribute("type") === "password") {
        champ.setAttribute("type", "text");
    } else {
        champ.setAttribute("type", "password");
    }
}

// verif que tous les champs sont bien validés et les stocke 
function validerFormulaire(event) {
    let prenomValide    = validerPrenom();
    let nomValide       = validerNom();
    let adresseValide   = validerAdresse();
    let telValide       = validerTelephone();
    let emailValide     = validerEmail();
    let mdpValide       = validerMotDePasse();

    
    if (!prenomValide || !nomValide || !adresseValide || !telValide || !emailValide || !mdpValide) {
        event.preventDefault(); // n'envoie pas le formulaire s'il y a un probleme avec un des champs 
    }
    // Si OK le formulaire s'envoie normalement vers le PHP
}


let champPrenom  = document.getElementById("idname");
let champNom     = document.getElementById("idfname");
let champAdresse = document.getElementById("idadr");
let champTel     = document.getElementById("idtel");
let champEmail   = document.getElementById("idemail");
let champMdp     = document.getElementById("idcode");
let formulaire   = document.getElementById("formulaire");



champPrenom.addEventListener("input", () => {
    mettreAJourCompteur(champPrenom, LIMITE_PRENOM);
    validerPrenom();
});

champNom.addEventListener("input", () => {
    mettreAJourCompteur(champNom, LIMITE_NOM);
    validerNom();
});

champAdresse.addEventListener("input", validerAdresse);

champTel.addEventListener("input", () => {
    mettreAJourCompteur(champTel, LIMITE_TEL);
    validerTelephone();
});

champEmail.addEventListener("input", () => {
    mettreAJourCompteur(champEmail, LIMITE_EMAIL);
    validerEmail();
});

champMdp.addEventListener("input", () => {
    mettreAJourCompteur(champMdp, LIMITE_MOT_DE_PASSE);
    validerMotDePasse();
});

//verifie avant d'envoyer le formulaire 
formulaire.addEventListener("submit", validerFormulaire);
