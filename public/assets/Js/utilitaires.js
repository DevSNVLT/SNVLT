
function dataTableSnvlt(name, title, taille){
    let table = new DataTable(name, {
        responsive: true,
        pageLength: taille,
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: title,
                        text: 'Exporter en excel'

                    }
                ]
            }
        },
        colReorder: true,
        language: {
            processing:     "Traitement en cours...",
            search:         "Rechercher&nbsp;:",
            lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
            info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            infoPostFix:    "",
            loadingRecords: "Chargement en cours...",
            zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable:     "Aucune donnée disponible dans le tableau",
            paginate: {
                first:      "Premier",
                previous:   "Pr&eacute;c&eacute;dent",
                next:       "Suivant",
                last:       "Dernier"
            },
            aria: {
                sortAscending:  ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        }
    });
}


function render_toast_value(valeur, type_valeur){
    let couleur = '#ffaeae'; // Erreur
    let couleur_texte = '#be0303'; // Erreur
    let icon = 'error';
    let iconColor = "red"
    valeur ='<span class="text-black" style="font-size: 16px;">' + valeur + '</span>'
    if(type_valeur === 1){ couleur_texte ='#2a7904'; couleur = '#cdfcb6'; icon = 'success';iconColor = '#2a7904'} // Succès
    else if (type_valeur === 2){couleur_texte ='#051000'; couleur = '#fada8c'; icon = 'warning'; iconColor = "#a87803"} // Avertissement
    else if (type_valeur === 3){couleur_texte ='#051000'; couleur = '#89d7bc'; icon = 'information'; iconColor = "#056b79"} // Infos
    Swal.fire({
        toast: true,
        background: couleur,
        color: couleur_texte,
        position: "top-end",
        modal : true,
        timer : 7000,
        icon: icon,
        iconColor: iconColor,
        title: valeur,
        showConfirmButton: false
    });
}


function getNbJours(date1, date2){
    const differenceInMilliseconds = date2.getTime() - date1.getTime(); // Conversion en millisecondes
    const differenceInDays = differenceInMilliseconds /  (1000 * 60 * 60 * 24); // Conversion en jours
        return differenceInDays;
}
