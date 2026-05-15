function livrerCommande() {
    window
        .fetch(new Request("livrer_commande.php"))

        .then((reponse) => {
            if (reponse == null) {
                document.getElementById("message-livraison").innerHTML = "Erreur, réessayez.";
                document.getElementById("message-livraison").classList.remove("cache");
            }
            else return reponse.json();
        })

        .then((reponse) => {
            if (reponse != null && reponse["succes"] == true) {
                document.querySelector(".btn-livraison").classList.add("cache");
                document.getElementById("message-livraison").classList.remove("cache");
            }
        })

        .catch((error) => {
            console.log("Erreur : " + error);
        });
}
