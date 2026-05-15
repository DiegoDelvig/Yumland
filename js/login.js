//les limites de caractère sont déjà dans le validation.js


function validerFormulaire(event) {
    let emailValide = validerEmail();
    let mdpValide   = validerMotDePasse();
 
    if (!emailValide || !mdpValide) {
        event.preventDefault();
    }
}
 
// on recupere les champs 
let champEmail = document.getElementById("idemail");
let champMdp   = document.getElementById("idcode");
let formulaire = document.getElementById("formulaire_login");
 
//mise a jour compteur 
champEmail.addEventListener("input", () => {
    mettreAJourCompteur(champEmail, LIMITE_EMAIL);
    validerEmail();
});
 
champMdp.addEventListener("input", () => {
    mettreAJourCompteur(champMdp, LIMITE_MOT_DE_PASSE);
    validerMotDePasse();
});
 
// vérifie le formulaire avant de l'envoyer
formulaire.addEventListener("submit", validerFormulaire);
