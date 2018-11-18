window.addEventListener('load', helpersList);

function helpersList() {
  [].slice.call(document.getElementsByClassName('assign-poi')).forEach(function(select) {
    select.addEventListener('input', function () {
      if (this.value !== 'null') {
        var option = document.getElementById(this.id).querySelector('[value="' + this.value + '"]');
        mapManager.map.setView(new  L.LatLng(option.dataset.latitude, option.dataset.longitude), 15);
      }

      let xhr_object = new XMLHttpRequest();
      xhr_object.open('PATCH', '/editor/raid/' + raidID + '/helper/' + this.id, true);
      xhr_object.setRequestHeader('Content-Type', 'application/json');
      let data;
      if (this.value === 'null') {
        data = 'null';
      } else {
        data = parseInt(this.value);
      }
      xhr_object.send(JSON.stringify({poi : data}));
    });
  })
}