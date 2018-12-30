window.addEventListener('load', displayModalToDeleteOrganizer);
window.addEventListener('load', displayModalToEnableOrganizer);
window.addEventListener('load', displayModalToDisableOrganizer);
window.addEventListener('load', displayModalToDeletePoiType);
window.addEventListener('load', displayModalToDeleteSportType);
window.addEventListener('load', displayModalToDeleteCollaborator);
window.addEventListener('load', displayModalToDeleteContact);

function displayModalToDeleteOrganizer () {
  var btns = document.querySelectorAll('.btn--delete-organizer');

  var username = '';
  var id = '';

  if (btns.length !== 0) {
      MicroModal.init();
      for (var btn of btns) {
          btn.addEventListener('click', function () {
              username = this.dataset.organizerUsername;
              document.getElementById('span--organizer-name').innerText = username;
              id = this.dataset.organizerId;
              MicroModal.show('delete-organizer');
          });
      }
  }

  document.getElementById('organizer-name-delete').addEventListener('keyup', function() {
      if (document.getElementById('organizer-name-delete').value !== username) {
          document.getElementById('btn--delete-organizer-validate').disabled = true;
      } else {
          document.getElementById('btn--delete-organizer-validate').disabled = false;
      }
  });

  document.getElementById('btn--delete-organizer-validate').addEventListener('click', function () {
      let xhr_object = new XMLHttpRequest();
      xhr_object.open('DELETE', '/admin/organizer/delete/' + id, true);
      xhr_object.setRequestHeader('Content-Type', 'application/json');

      xhr_object.send(null);

      MicroModal.close('delete-organizer');
      document.getElementById('organizer-' + id).remove();

      iziToast.success({
          message: 'L\'utilisateur ' + username + ' a bien été supprimé.',
          position: 'bottomRight',
      });

  });
}

function displayModalToEnableOrganizer () {
    var btns = document.querySelectorAll('.btn--enable-organizer');

    if (btns.length !== 0) {
        MicroModal.init();
        for (var btn of btns) {
            btn.addEventListener('click', function () {
                var id = this.dataset.organizerId;
                var url = document.getElementById('btn--enable-organizer-validate').dataset.baseUrl + id + '/1';
                document.getElementById('btn--enable-organizer-validate').href = url;
                MicroModal.show('enable-organizer');
            });
        }
    }
}

function displayModalToDisableOrganizer () {
    var btns = document.querySelectorAll('.btn--disable-organizer');

    if (btns.length !== 0) {
        MicroModal.init();
        for (var btn of btns) {
            btn.addEventListener('click', function () {
                var id = this.dataset.organizerId;
                var url = document.getElementById('btn--disable-organizer-validate').dataset.baseUrl + id + '/0';
                document.getElementById('btn--disable-organizer-validate').href = url;
                MicroModal.show('disable-organizer');
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
