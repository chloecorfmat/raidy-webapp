window.addEventListener('load', displayModalToDelete)
window.addEventListener('load', displayModalToDeletePoiType)
window.addEventListener('load', displayModalToDeleteSportType)

function displayModalToDelete () {
  MicroModal.init();

  document.querySelectorAll('.btn--delete-organizer').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = this.dataset.organizerId;
      var url = document.getElementById('btn--delete-organizer').dataset.baseUrl + id;
      document.getElementById('btn--delete-organizer').href = url;
      MicroModal.show('delete-organizer');
    })
  })
}

function displayModalToDeletePoiType () {
  MicroModal.init() // eslint-disable-line no-undef

  document.querySelectorAll('.btn--delete-poitype').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = this.dataset.poitypeId
      var url = document.getElementById('btn--delete-poitype').dataset.baseUrl + id
      document.getElementById('btn--delete-poitype').href = url
      MicroModal.show('delete-poitype') // eslint-disable-line no-undef
    })
  })
}

function displayModalToDeleteSportType () {
  MicroModal.init() // eslint-disable-line no-undef

  document.querySelectorAll('.btn--delete-sporttype').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = this.dataset.sporttypeId
      var url = document.getElementById('btn--delete-sporttype').dataset.baseUrl + id
      document.getElementById('btn--delete-sporttype').href = url
      MicroModal.show('delete-sporttype') // eslint-disable-line no-undef
    })
  })
}
