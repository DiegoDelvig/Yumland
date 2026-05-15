
//script commun pour login et inscription

// Limite caractère
let LIMITE_EMAIL = 100;
let LIMITE_MOT_DE_PASSE = 50;
 
// affiche ou efface un message d'erreur sous un champ
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
 
// met à jour le compteur de caractères
function mettreAJourCompteur(champ, limite) {
    let compteurElement = document.getElementById("compteur_" + champ.id);
    if (compteurElement !== null) {
        let utilises = champ.value.length;
        compteurElement.innerHTML = utilises + " / " + limite + " caractères";
 
        if (utilises > limite) {
            compteurElement.classList.add("compteur_depasse");
        } else {
            compteurElement.classList.remove("compteur_depasse");
        }
    }
}
 
// valide l'email
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
 
    // verif point après @
    let apresArobase = parties[1];
    if (!apresArobase.includes(".")) {
        afficherErreur(champ, "L'adresse email n'est pas valide (ex: nom@domaine.fr).");
        return false;
    }
 
    // verif pas de champ vide
    if (parties[0] === "" || apresArobase === "" || apresArobase.endsWith(".")) {
        afficherErreur(champ, "L'adresse email n'est pas valide (ex: nom@domaine.fr).");
        return false;
    }
 
    afficherErreur(champ, "");
    return true;
}
 
// valide le mot de passe
function validerMotDePasse() {
    let champ = document.getElementById("idcode");
    let valeur = champ.value;
 
    if (valeur === "") {
        afficherErreur(champ, "Le mot de passe est obligatoire.");
        return false;
    }
 
    afficherErreur(champ, "");
    return true;
}
 
// bascule affichage mot de passe
function basculerVisibiliteMotDePasse() {
    let champ = document.getElementById("idcode");
 
    if (champ.getAttribute("type") === "password") {
        champ.setAttribute("type", "text");
    } else {
        champ.setAttribute("type", "password");
    }
}
