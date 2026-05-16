async function bloquerUtilisateur(email, bouton) {
  try {
    const formData = new FormData();
    formData.append("email", email);

    const response = await fetch("api_bloquer_utilisateur.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      // Mettre à jour le bouton
      if (data.bloque) {
        bouton.textContent = "🔓 Débloquer";
        bouton.classList.add("bloque");
      } else {
        bouton.textContent = "🔒 Bloquer";
        bouton.classList.remove("bloque");
      }

      alert(data.message + " : " + data.nom);

      setTimeout(() => location.reload(), 1000);
    } else {
      alert("Erreur : " + data.message);
    }
  } catch (error) {
    console.error("Erreur:", error);
    alert("Erreur réseau");
  }
}
