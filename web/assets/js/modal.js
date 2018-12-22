window.addEventListener('load', displayModalToDelete);
window.addEventListener('load', displayModalToDeletePoiType);
window.addEventListener('load', displayModalToDeleteSportType);
window.addEventListener('load', displayModalToDeleteCollaborator);
window.addEventListener('load', displayModalToDeleteContact);

function displayModalToDelete () {
  var btns = document.querySelectorAll('.btn--delete-organizer');

  if (btns.length !== 0) {
      MicroModal.init();
      for (var btn of btns) {
          btn.addEventListener('click', function () {
              var id = this.dataset.organizerId;
              var url = document.getElementById('btn--delete-organizer').dataset.baseUrl + id;
              document.getElementById('btn--delete-organizer').href = url;
              MicroModal.show('delete-organizer');
          });
      }
  }

}

function displayModalToDeletePoiType () {
  var btns = document.querySelectorAll('.btn--delete-poitype');

  if (btns.length !== 0) {
      MicroModal.init();
      for (var btn of btns) {
          btn.addEventListener('click', function () {
              var id = this.dataset.poitypeId;
              var url = document.getElementById('btn--delete-poitype').dataset.baseUrl + id;
              document.getElementById('btn--delete-poitype').href = url;
              MicroModal.show('delete-poitype'); // eslint-disable-line no-undef
          });
      }
  }
}

function displayModalToDeleteSportType () {
  var btns = document.querySelectorAll('.btn--delete-sporttype');

  if (btns.length !== 0) {
      MicroModal.init();

      for (var btn of btns) {
          btn.addEventListener('click', function () {
              var id = this.dataset.sporttypeId;
              var url = document.getElementById('btn--delete-sporttype').dataset.baseUrl + id;
              document.getElementById('btn--delete-sporttype').href = url;
              MicroModal.show('delete-sporttype'); // eslint-disable-line no-undef
          });
      }
  }
}

function displayModalToDeleteCollaborator () {
  var btns = document.querySelectorAll('.btn--delete-collaborator');

  if (btns.length !== 0) {
      MicroModal.init();
      for (var btn of btns) {
          btn.addEventListener('click', function () {
              var url = '/editor/raid/' + this.dataset.raid + '/collaborator/' + this.dataset.invitation + '/delete';
              document.getElementById('btn--delete-collaborator').href = url;
              MicroModal.show('delete-collaborator'); // eslint-disable-line no-undef
          });
      }
  }

}

function displayModalToDeleteContact () {
  var btns = document.querySelectorAll('.btn--delete-contact');

  if (btns.length !== 0) {
      MicroModal.init();
      for (var btn of btns) {
          btn.addEventListener('click', function () {
              var id = this.dataset.contactId;
              var url = document.getElementById('btn--delete-contact').dataset.baseUrl + id;
              document.getElementById('btn--delete-contact').href = url;
              MicroModal.show('delete-contact'); // eslint-disable-line no-undef
          });
      }
  }
}
