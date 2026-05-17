document.addEventListener("DOMContentLoaded", function () {
    const textareas = document.querySelectorAll("textarea.com_1");

    textareas.forEach(textarea => {
        textarea.addEventListener("input", function () {
            const maxLength = this.getAttribute("maxlength");
            const currentLength = this.value.length;
            const compteurId = "compteur_" + this.id;
            const compteurElement = document.getElementById(compteurId);

            if (compteurElement) {
                compteurElement.textContent = `${currentLength} / ${maxLength} caractères`;
                
                if (currentLength >= maxLength) {
                    compteurElement.style.color = "red";
                    compteurElement.style.fontWeight = "bold";
                } else {
                    compteurElement.style.color = "#666";
                    compteurElement.style.fontWeight = "normal";
                }
            }
        });
    });
    const formulaire = document.getElementById("formulaire_notation");
    let isSubmitting = false; 

    if (formulaire) {
        formulaire.addEventListener("submit", function (event) {
            if (isSubmitting) {
                event.preventDefault();
                return;
            }
            isSubmitting = true;
            
            const submitBtn = formulaire.querySelector(".avis_submit");
            if(submitBtn){
                submitBtn.style.pointerEvents = "none";
                submitBtn.textContent = "Envoi en cours...";
                submitBtn.style.opacity = "0.7";
            }
        });
    }
});
