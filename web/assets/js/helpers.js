window.addEventListener('load', helpersList);

function helpersList() {
  // Assign POI.
  var selects = document.getElementsByClassName('assign-poi');

  for (var select of selects) {
    select.addEventListener('input', function () {
      if (this.value !== 'null') {
        var option = document.getElementById(this.id).querySelector('[value="' + this.value + '"]');
        mapManager.map.setView(new  L.LatLng(option.dataset.latitude, option.dataset.longitude), 15);
      }

      let xhr_object = new XMLHttpRequest();
      xhr_object.open('PATCH', '/editor/raid/' + raidID + '/helper/' + this.id, true);
      xhr_object.setRequestHeader('Content-Type', 'application/json');
      let data;

      var helper = document.getElementById('status-' + this.id);
      var status = helper.querySelector('.helper-check');
      var text = helper.querySelector('.helper-check--text');
      var helperValidate = null;

      if (this.value === 'null') {
        data = 'null';
        if (status.classList.contains('helper-check--not')) {
          status.classList.remove('helper-check--not');
          status.classList.add('helper-check--no-assign');
          text.innerHTML = 'Aucun POI assigné';

          helperValidate = helper.parentNode.querySelector('.helper-validate');
          helperValidate.innerHTML = '';

          var spanContent = document.createElement('span');
          spanContent.classList.add('helper-validate');
          spanContent.classList.add('helper-validate--no-assign');
          spanContent.innerText = '-';

          var spanSrOnly = document.createElement('span');
          spanSrOnly.classList.add('helper-validate--text');
          spanSrOnly.classList.add('sr-only');
          spanSrOnly.innerText = "Aucun POI assigné";

          helperValidate.appendChild(spanContent);
          helperValidate.appendChild(spanSrOnly);
        }
      } else {
        data = parseInt(this.value);
          if (status.classList.contains('helper-check--no-assign')) {
              status.classList.remove('helper-check--no-assign');
              status.classList.add('helper-check--not');
              text.innerHTML = 'Non validé';
          }

          helperValidate = helper.parentNode.querySelector('.helper-validate');
          helperValidate.innerHTML = '';

          var btnValidate = document.createElement('button');
          btnValidate.classList.add('btn');
          btnValidate.classList.add('btn-validate-helper');
          btnValidate.dataset.helperid = this.id;
          btnValidate.dataset.raidid = raidID;

          btnValidate.innerText = "Valider";

          helperValidate.appendChild(btnValidate);

          btnValidate.addEventListener('click', validateHelper);


          // <button class="btn btn-validate-helper" data-helperid="{{ helper.id }}" data-raidid="{{ raid_id }}">Valider</button>
      }
      xhr_object.send(JSON.stringify({poi : data}));
      console.log(mapManager);
      mapManager.reloadPois();

      iziToast.success({
          message: 'Les modifications ont bien été enregistrées.',
          position: 'bottomRight',
      });

      //location.reload(true);
    });
  };

  // Check in manually.
  var validateBtns = document.getElementsByClassName('btn-validate-helper');

  for (var btn of validateBtns) {
    btn.addEventListener('click', validateHelper);
  }
}

function validateHelper(e) {
    let xhr_object = new XMLHttpRequest();

    xhr_object.onreadystatechange = function() {
        if (xhr_object.readyState === 4 && xhr_object.status === 200) {
            var response = JSON.parse(xhr_object.response);
            var date = new Date(response.checkInTime.date);
            var button = document.querySelector('[data-helperid="' + response.helperId + '"]');
            button.parentNode.parentNode.querySelector('.assigned-poi select').disabled = true;
            button.parentNode.parentNode.querySelector('.status').innerHTML = "<span class=\"helper-check helper-check--in\"></span>\n" +
                "<span class=\"helper-check--text sr-only\">Validé</span>";
            button.parentNode.innerHTML = '<span>' + date.toLocaleTimeString('fr-FR') + '</span>';

            iziToast.success({
                message: 'Le statut a été enregistré.',
                position: 'bottomRight',
            });
        } else if (xhr_object.readyState === 4) {
            iziToast.error({
                message: 'Un problème est survenu, veuillez réessayer plus tard.',
                position: 'bottomRight',
            });
        }
    }

    xhr_object.open('PATCH', '/organizer/raid/' + this.dataset.raidid + '/helper/' + this.dataset.helperid + '/checkin', true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send();
}
