window.addEventListener('load', organizersList)
window.addEventListener('load', helpers)
window.addEventListener('load', poiTypesList)

var options = {
  labels: {
    placeholder: 'Rechercher...',
    perPage: '{select} résultats par page',
    noRows: 'Aucun résultat trouvé',
    info: 'Affichage des entrées {start} à {end} sur {rows} entrées totales'
  }
}

function organizersList (e) {
  if (document.getElementById('organizersList') != null) {
    var dataTableOrganizer = new DataTable('#organizersList', options) // eslint-disable-line no-undef, no-new

    dataTableOrganizer.on('datatable.page', function (page) {
      // Needed to display modal to delete organizer.
      displayModalToDelete() // eslint-disable-line no-undef
    })
  }
}

function helpers (e) {
  if (document.getElementById('helpersList') != null) {
    new DataTable('#helpersList', options) // eslint-disable-line no-undef, no-new
  }
}

function poiTypesList (e) {
  if (document.getElementById('poiTypesList') != null) {
    new DataTable('#poiTypesList', options) // eslint-disable-line no-undef, no-new
  }
}
