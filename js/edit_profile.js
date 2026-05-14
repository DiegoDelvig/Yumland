document.addEventListener("DOMContentLoaded", function () {
  // Éléments du DOM
  const btnEditProfile = document.getElementById("btn-edit-profile");
  const modalEditProfile = document.getElementById("modal-edit-profile");
  const modalOverlay = document.getElementById("modal-overlay");
  const closeModalBtn = document.getElementById("close-modal");
  const formEditProfile = document.getElementById("form-edit-profile");
  const btnCancelForm = document.getElementById("btn-cancel-form");
  const btnSave = document.getElementById("btn-save");
  const formMessage = document.getElementById("form-message");
  const textareaInfocomp = document.getElementById("edit-infocomp");
  const charCount = document.getElementById("char-count");

  // Vérifier que tous les éléments existent
  if (!btnEditProfile || !modalEditProfile) {
    console.warn("Modal edit profile elements not found");
    return;
  }

  // Ouvrir le modal
  function openModal() {
    modalEditProfile.style.display = "flex";
    modalOverlay.style.display = "block";
    document.body.style.overflow = "hidden";
    formMessage.innerHTML = "";
    formMessage.className = "form-message";
  }

  // Fermer le modal
  function closeModal() {
    modalEditProfile.style.display = "none";
    modalOverlay.style.display = "none";
    document.body.style.overflow = "";
    clearErrors();
  }

  // Événements pour ouvrir/fermer
  btnEditProfile.addEventListener("click", openModal);
  closeModalBtn.addEventListener("click", closeModal);
  btnCancelForm.addEventListener("click", closeModal);
  modalOverlay.addEventListener("click", closeModal);

  // Compteur de caractères
  if (textareaInfocomp && charCount) {
    textareaInfocomp.addEventListener("input", function () {
      charCount.textContent = `${this.value.length}/500`;
    });
    // Initialiser au chargement
    charCount.textContent = `${textareaInfocomp.value.length}/500`;
  }

  // Valider un champ
  function validateField(fieldName, value) {
    const errors = {};

    switch (fieldName) {
      case "name":
      case "fname":
        if (value.trim().length < 2) {
          errors[fieldName] = "Minimum 2 caractères requis";
        } else if (value.length > 100) {
          errors[fieldName] = "Maximum 100 caractères";
        }
        break;

      case "adr":
        if (value.trim().length < 5) {
          errors[fieldName] = "Veuillez entrer une adresse valide";
        } else if (value.length > 200) {
          errors[fieldName] = "Maximum 200 caractères";
        }
        break;

      case "tel":
        // Accepte formats: 0601020304, +33601020304, 06 01 02 03 04, etc.
        const telRegex = /^[0-9\s+()-]{9,20}$/;
        if (!telRegex.test(value)) {
          errors[fieldName] = "Numéro de téléphone invalide";
        }
        break;

      case "infocomp":
        if (value.length > 500) {
          errors[fieldName] = "Maximum 500 caractères";
        }
        break;
    }

    return errors;
  }

  // Afficher les erreurs
  function showErrors(errors) {
    clearErrors();
    Object.keys(errors).forEach((fieldName) => {
      const errorEl = document.getElementById(`error-${fieldName}`);
      const inputEl = document.getElementById(`edit-${fieldName}`);
      if (errorEl && inputEl) {
        errorEl.textContent = errors[fieldName];
        errorEl.classList.add("show");
        inputEl.classList.add("error");
      }
    });
  }

  // Effacer les erreurs
  function clearErrors() {
    document.querySelectorAll(".error-message").forEach((el) => {
      el.classList.remove("show");
      el.textContent = "";
    });
    document.querySelectorAll("input.error, textarea.error").forEach((el) => {
      el.classList.remove("error");
    });
  }

  // Soumettre le formulaire
  formEditProfile.addEventListener("submit", async function (e) {
    e.preventDefault();
    clearErrors();
    formMessage.innerHTML = "";
    formMessage.className = "form-message";

    // Récupérer les données
    const formData = new FormData(formEditProfile);
    const data = {
      name: formData.get("name"),
      fname: formData.get("fname"),
      adr: formData.get("adr"),
      tel: formData.get("tel"),
      infocomp: formData.get("infocomp") || "",
    };

    // Valider tous les champs
    let allErrors = {};
    Object.keys(data).forEach((fieldName) => {
      allErrors = {
        ...allErrors,
        ...validateField(fieldName, data[fieldName]),
      };
    });

    if (Object.keys(allErrors).length > 0) {
      showErrors(allErrors);
      return;
    }

    // Désactiver le bouton
    btnSave.disabled = true;
    document.getElementById("save-text").style.display = "none";
    document.getElementById("save-loading").style.display = "inline";

    try {
      const response = await fetch("api/update_profile.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (response.ok && result.success) {
        // Succès
        formMessage.textContent =
          result.message || "Profil mis à jour avec succès ! ✅";
        formMessage.className = "form-message success";

        // Mettre à jour l'affichage du profil
        updateProfileDisplay(result.data);

        // Fermer le modal après 2 secondes
        setTimeout(() => {
          closeModal();
          // Recharger la page pour voir les changements
          location.reload();
        }, 2000);
      } else {
        // Erreur serveur
        formMessage.textContent =
          result.error || "Erreur lors de la sauvegarde";
        formMessage.className = "form-message error";
      }
    } catch (error) {
      console.error("Erreur AJAX:", error);
      formMessage.textContent =
        "Erreur de connexion. Vérifiez votre connexion internet.";
      formMessage.className = "form-message error";
    } finally {
      // Réactiver le bouton
      btnSave.disabled = false;
      document.getElementById("save-text").style.display = "inline";
      document.getElementById("save-loading").style.display = "none";
    }
  });

  // Mettre à jour l'affichage du profil en direct (optionnel)
  function updateProfileDisplay(clientData) {
    const infoDetails = document.querySelector(".info-details");
    if (infoDetails) {
      infoDetails.innerHTML = `
                <p>NOM :</p>
                <p><strong>${escapeHtml(clientData.fname)}</strong></p>
                <p>PRÉNOM :</p>
                <p><strong>${escapeHtml(clientData.name)}</strong></p>
                <p>ADRESSE :</p>
                <p><strong>${escapeHtml(clientData.adr)}</strong></p>
                <p>TÉLÉPHONE :</p>
                <p><strong>${escapeHtml(clientData.tel)}</strong></p>
                ${
                  clientData.infocomp
                    ? `
                    <p>INFOS COMPLÉMENTAIRES :</p>
                    <p><strong>${escapeHtml(clientData.infocomp)}</strong></p>
                `
                    : ""
                }
            `;
    }
  }

  // Échapper les caractères HTML (sécurité)
  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }
});
