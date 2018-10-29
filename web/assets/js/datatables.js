window.addEventListener('load', organizersList)
window.addEventListener('load', helpers)

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
    new DataTable('#organizersList', options) // eslint-disable-line no-undef, no-new
  }
}

function helpers (e) {
  if (document.getElementById('helpersList') != null) {
    new DataTable('#helpersList', options) // eslint-disable-line no-undef, no-new
  }
}
