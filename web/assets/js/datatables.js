window.addEventListener('load', organizersList);
window.addEventListener('load', helpers);
window.addEventListener('load', poiTypesList);
window.addEventListener('load', sportTypesList);
window.addEventListener('load', contactsList);

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
    var dataTableOrganizer = new DataTable('#organizersList', options);

    dataTableOrganizer.on('datatable.page', function (page) {
      // Needed to display modal to delete organizer.
      displayModalToDelete();
    })
  }
}

function helpers (e) {
  if (document.getElementById('helpersList') != null) {
    var d = new DataTable('#helpersList', options);
  }
}

function poiTypesList (e) {
  if (document.getElementById('poiTypesList') != null) {
    var dataTablePoiType = new DataTable('#poiTypesList', options); // eslint-disable-line no-undef, no-new

    dataTablePoiType.on('datatable.page', function (page) {
      // Needed to display modal to delete poi type
      displayModalToDeletePoiType(); // eslint-disable-line no-undef
    })
  }
}

function sportTypesList (e) {
  if (document.getElementById('sportTypesList') != null) {
    var dataTableSportType = new DataTable('#sportTypesList', options); // eslint-disable-line no-undef, no-new

    dataTableSportType.on('datatable.page', function (page) {
      // Needed to display modal to delete sport type.
      displayModalToDeleteSportType(); // eslint-disable-line no-undef
    })
  }
}

function contactsList (e) {
  if (document.getElementById('contactsList') != null) {
    var dataTableContact = new DataTable('#contactsList', options); // eslint-disable-line no-undef, no-new

    dataTableContact.on('datatable.page', function (page) {
      // Needed to display modal to delete poi type
      displayModalToDeleteContact(); // eslint-disable-line no-undef
    })
  }
}
