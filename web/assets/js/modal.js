window.addEventListener('load', displayModalToDelete)
window.addEventListener('load', displayModalToDeletePoiType)

function displayModalToDelete () {
  MicroModal.init() // eslint-disable-line no-undef

  document.querySelectorAll('.btn--delete-organizer').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = this.dataset.organizerId
      var url = document.getElementById('btn--delete-organizer').dataset.baseUrl + id
      document.getElementById('btn--delete-organizer').href = url
      MicroModal.show('delete-organizer') // eslint-disable-line no-undef
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
