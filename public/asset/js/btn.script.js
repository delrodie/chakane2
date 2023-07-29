document.addEventListener("DOMContentLoaded", function() {
    var saveButton = document.getElementById("saveButton");
    var deleteButton = document.getElementById("deleteButton");
    var deleteForm = document.getElementById("deleteForm")

    saveButton.addEventListener("click", function() {
        saveButton.classList.add("button-processing");
        saveButton.innerHTML = "Sauvegarde en cours...";
    });

    deleteForm.addEventListener("submit", function(event) {
        // var confirmation = confirm("Voulez-vous vraiment supprimer cet element?");
        // if (!confirmation){
        //     event.preventDefault();
        //     return;
        //     console.log("annuler")
        // }

        event.preventDefault()

        Swal.fire({
            title: 'Attention',
            text: "Voulez-vous vraiment supprimer cet element?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: "Annuler"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteForm.submit();
                deleteButton.classList.add("button-processing");
                deleteButton.innerHTML = "Suppression en cours...";
                Swal.fire(
                    'Supprimé!',
                    'L\'élément a été supprimé avec succès.',
                    'success'
                )
            }
        })
    });

    // Affichage du modal de modification
    var modal = new bootstrap.Modal(document.getElementById("primary-header-modal"));
    modal.show();
});




