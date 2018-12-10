window.addEventListener('load', helpersList);

function helpersList() {
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

      if (this.value === 'null') {
        data = 'null';
        if (status.classList.contains('helper-check--not')) {
          status.classList.remove('helper-check--not');
          status.classList.add('helper-check--no-assign');
          text.innerHTML = 'Aucun POI assigné';
        }
      } else {
        data = parseInt(this.value);
          if (status.classList.contains('helper-check--no-assign')) {
              status.classList.remove('helper-check--no-assign');
              status.classList.add('helper-check--not');
              text.innerHTML = 'Non validé';
          }
      }
      xhr_object.send(JSON.stringify({poi : data}));
      //location.reload(true);
    });
  };
}
