
let LIMITE_PRENOM = 50;
let LIMITE_NOM = 50;
let LIMITE_TEL = 14;

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
    if (valeur.length < 7 || valeur.length > LIMITE_TEL) {
        afficherErreur(champ, "Le numéro doit contenir entre 7 et 14 caractères.");
        return false;
    }
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

function validerFormulaire(event) {
    let prenomValide  = validerPrenom();
    let nomValide     = validerNom();
    let adresseValide = validerAdresse();
    let telValide     = validerTelephone();
    if (!prenomValide || !nomValide || !adresseValide || !telValide) {
        event.preventDefault();
    }
}

let champPrenom  = document.getElementById("idname");
let champNom     = document.getElementById("idfname");
let champAdresse = document.getElementById("idadr");
let champTel     = document.getElementById("idtel");
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

formulaire.addEventListener("submit", validerFormulaire);
