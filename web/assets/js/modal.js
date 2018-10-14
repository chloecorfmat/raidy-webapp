window.addEventListener('load', function () {
  MicroModal.init()

  document.querySelectorAll('.btn--delete-organizer').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = this.dataset.organizerId
      var url = document.getElementById('btn--delete-organizer').dataset.baseUrl + id
      document.getElementById('btn--delete-organizer').href = url
      MicroModal.show('delete-organizer')
    })
  })
})
