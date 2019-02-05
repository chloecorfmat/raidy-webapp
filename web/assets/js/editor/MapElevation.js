let MapElevation;
if(typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  MapElevation = function () {
    //this.API_KEY = 'j2z0s6csrgwdg2f5gl7gp2my';

    let ctx = document.getElementById('canvas').getContext('2d');
    var config = {
      type: 'scatter',
      data: {
        labels: [],
        datasets: [{
          label: 'Cubic interpolation (monotone)',
          data: [],
          borderColor: '#333',
          backgroundColor: 'rgba(0, 0, 1, 0.2)',
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




    this.chart = new Chart(ctx, config);

    window.myLine = this.chart;
  }

  MapElevation.prototype.getElevationAt = function (latlng, callback) {

    Gp.Services.getAltitude({
      apiKey: IGNAPIKEY, // clef d'accès à la plateforme
      positions: [                        // positions pour le calcul alti
        {lon: latlng.lng, lat: latlng.lat}
      ],
      sampling: 50,                      // nombre de points pour le profil
      onSuccess: function (result) {
        let alti = result['elevations'][0].z;
        // exploitation des resultats : "result" est de type Gp.Services.AltiResponse
        latlng.alt = alti;
      //  console.log(alti);
        callback();
      },
      onFailure : function () {
        iziToast.error({
          message: 'Erreur dans le calcul de l\'altitude. Vérifier votre connexion internet.',
          position: 'bottomLeft',
        });
      }
    });
  }

  MapElevation.prototype.initChart = function (track) {
    var datapoints = [];
    let labels = [];
    let dist = 0;
    let i = 0;
    let lastPoint;
    if (track != undefined) {
      for (let obj of track.line.getLatLngs()) {
        datapoints.push({x: dist/1000, y: obj.alt});
        //datapoints.push(obj.ele);
        labels.push(i);
        if(lastPoint !=null){
          dist += this.calcDistanceBetween(lastPoint, obj);
        }
        lastPoint = obj;
        i++;
      }
    }
   // console.log(datapoints);
    let data = {
      datasets: [{
        label: 'Cubic interpolation (monotone)',
        data: datapoints,
        borderColor: track.color,
        backgroundColor: 'rgba(0, 1, 0, 0.2)',
        pointRadius : 0,
        fill: true,
        showLine : true,
        cubicInterpolationMode: 'default'
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
    };

    this.chart.data = data;
    this.chart.update();
  }

  MapElevation.prototype.calcDistanceBetween = function (wpt1, wpt2) {
    let latlng1 = {};
    latlng1.lat = wpt1.lat;
    latlng1.lng = wpt1.lng;
    let latlng2 = {};
    latlng2.lat = wpt2.lat;
    latlng2.lng = wpt2.lng;
    var rad = Math.PI / 180,
      lat1 = latlng1.lat * rad,
      lat2 = latlng2.lat * rad,
      sinDLat = Math.sin((latlng2.lat - latlng1.lat) * rad / 2),
      sinDlng = Math.sin((latlng2.lng - latlng1.lng) * rad / 2),
      a = sinDLat * sinDLat + Math.cos(lat1) * Math.cos(lat2) * sinDlng * sinDlng,
      c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return 6371000 * c;
  }

};