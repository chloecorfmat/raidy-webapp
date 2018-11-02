window.addEventListener('load', organizersList)
window.addEventListener('load', helpers)
window.addEventListener('load', poiTypesList)
window.addEventListener('load', sportTypesList)

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
    var dataTablePoiType = new DataTable('#poiTypesList', options) // eslint-disable-line no-undef, no-new

    dataTablePoiType.on('datatable.page', function (page) {
      /* document.getElementsByClassName('char--poitype').forEach(function () {
        console.log('salut')
        var id = this.dataset.poitypeId
        console.log(id)
        var colorId = document.getElementById('char--poitype-') + id
        colorId.color = this.dataset.poitypecolor
        console.log(this.dataset)
      }) */
      // Needed to display modal to delete poi type
      displayModalToDeletePoiType() // eslint-disable-line no-undef
    })
  }
}

function sportTypesList (e) {
  if (document.getElementById('sportTypesList') != null) {
    var dataTableSportType = new DataTable('#sportTypesList', options) // eslint-disable-line no-undef, no-new

    dataTableSportType.on('datatable.page', function (page) {
      // Needed to display modal to delete sport type.
      displayModalToDeleteSportType() // eslint-disable-line no-undef
    })
  }
}
