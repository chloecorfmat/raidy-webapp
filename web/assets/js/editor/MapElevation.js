let MapElevation;
if(typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  MapElevation = function () {
    //this.API_KEY = 'j2z0s6csrgwdg2f5gl7gp2my';
  }

  MapElevation.prototype.getElevationAt = function (latlng, callback) {

    Gp.Services.getAltitude({
      apiKey : "choisirgeoportail", // clef d'accès à la plateforme
      positions : [                        // positions pour le calcul alti
        { lon: latlng.lng, lat: latlng.lat}
      ],
      sampling : 50,                      // nombre de points pour le profil
      onSuccess : function (result) {
      let alti = result['elevations'][0].z;
        // exploitation des resultats : "result" est de type Gp.Services.AltiResponse
        latlng.ele = alti;
        console.log(alti);
        callback();
      }
    });




   /* let xhr_object = new XMLHttpRequest();
    let parameters= 'https://wxs.ign.fr/'+this.API_KEY+'/alti/rest/elevation.json?lon='+latlng.lat+'&lat='+latlng.lng+'&zonly=true';
    //https)://wxs.ign.fr/CLEF/alti/rest/elevation.json?lon=0.2367|2.1570&lat=48.0551|46.6077&zonly=true
    //xhr_object.open('GET', 'https://maps.googleapis.com/maps/api/elevation/json?'+parameters, true);
    xhr_object.open('GET', parameters, true);

    xhr_object.setRequestHeader("Access-Control-Allow-Origin", "*");
    xhr_object.setRequestHeader("Content-Type", "Application/json");
    xhr_object.send(null);
    xhr_object.onreadystatechange = function (event) {
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          let results = JSON.parse(xhr_object.responseText);
          for (let result of results) {
            console.log(result);
          }
        }
      }
    }
*/
  }

  MapElevation.prototype.initChart = function(track) {
    var datapoints = [0, 20, 20, 60, 60, 120, 200, 180, 120, 125, 105, 110, 170];
    let dist = 0;
    let lastPoint
    for (let obj of this.line.getLatLngs()) {
      datapoints.push(obj.ele);
      datapoints.push(obj.ele);
      i++;
    }
    var config = {
      type: 'line',
      data: {
        labels: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
        datasets: [{
          label: 'Cubic interpolation (monotone)',
          data: datapoints,
          borderColor: track.color,
          backgroundColor: 'rgba(0, 0, 0, 0.2)',
          fill: true,
          cubicInterpolationMode: 'monotone'
        }/*, {
          label: 'Cubic interpolation (default)',
          data: datapoints,
          borderColor: "blue",
          backgroundColor: 'rgba(0, 0, 0, 0)',
          fill: false,
        }, *//*{
          label: 'Linear interpolation',
          data: datapoints,
          borderColor: "green",
          backgroundColor: 'rgba(0, 0, 0, 0)',
          fill: false,
          lineTension: 0
        }*/]
      },
      options: {
        responsive: true,
        legend: {
          display: false,
          labels: {
            fontColor: 'rgb(255, 99, 132)'
          }
        },
        scales: {
          xAxes: [{
            display: true,
            scaleLabel: {
              display: true
            }
          }],
          yAxes: [{
            display: true,
            scaleLabel: {
              display: true,
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 200,
            }
          }]
        }
      }
    };
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, config);
  }
}