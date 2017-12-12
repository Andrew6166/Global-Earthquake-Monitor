<?php
error_reporting(0); // dev
    if(!isset($_REQUEST['sigTime'])) { // default time frame for data retrieval
        $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_week.geojson';
    }
    $url = $_REQUEST['sigTime']; // get time frame
    $mag = $_REQUEST['mag']; //get magnitude
    $dataurl = "";
    if(!isset($mag)) { // mag ~ if magnitude not specified, get significant events only
        switch ($url) {
            case 'pastweek':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_week.geojson';
                break;
            case 'pastday':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_day.geojson';
                break;

            case 'pasthour':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_hour.geojson';
                break;
            case 'pastmonth':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_month.geojson';
                break;
            default:
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_week.geojson';
        }
    } else { // mag ~ if mag specified get specific magnitude different time frames
        switch ($url) {
            case 'pasthour':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/'.$mag.'_hour.geojson';
                break;
            case 'pastweek':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/'.$mag.'_week.geojson';
                break;
            case 'pastday':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/'.$mag.'_day.geojson';
                break;
            case 'pastmonth':
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/'.$mag.'_month.geojson';
                break;
            default:
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/4.5_hour.geojson'; //default ~ get above 4.5 magnitude last hour
        }
    }
?>
<!DOCTYPE HTML>
<html>

<head>
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <!--https://earthquake.usgs.gov/fdsnws/event/1/query?format=geojson&starttime=2014-01-01&endtime=2014-01-02-->
  <script src="v2/api.js"></script>
  <script>

      /**GUP: GET URL PARAMETERS**/
      function gup() {
          var qs = document.location.search;
          qs = qs.split('+').join(' ');
          var params = {}, tokens, re = /[?&]?([^=]+)=([^&]*)/g;
          while (tokens = re.exec(qs))
              params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
          return params;
      }

      function getWinUrl() {
        var params = gup();
            window.location= '//'+'<?=$_SERVER['SERVER_NAME']?>/earth.php'+'';
      }

      function formatDate(date) {
          var d = new Date(date),
              month = '' + (d.getMonth() + 1),
              day = '' + d.getDate(),
              year = d.getFullYear();

          if (month.length < 2) month = '0' + month;
          if (day.length < 2) day = '0' + day;

          return [year, month, day].join('-');
      }

      function initialize(time) {
          if (window.location.href.substr(0, 5) === 'file:')
              alert("This file must be accessed via http:// or https:// to run properly.");
          var mapOptions = {
              sky: true,
              atmosphere: true
          };
          var earth = new WE.map('earth_div', mapOptions);
          earth.setView([46.8011, 8.2266], 2);
          WE.tileLayer('{z}/{x}/{y}.jpg', {
              tileSize: 256,
              bounds: [
                  [-85, -180],
                  [85, 180]
              ],
              minZoom: 1,
              maxZoom: 16,
              attribution: 'global earthquake monitor',
              tms: true
          }).addTo(earth);


          var xhr = new XMLHttpRequest();
          if (time) {
            var d = new Date();
            var startFin = "";
            var endFin = "";
            var formattedDateStart = "";
            var formattedDateEnd = "";
            var start = "";
            var end = "";
              switch (time) {
                  case 'Last Week':
                      startFin = d.setDate(d.getDate() - 7);
                      formattedDateStart = formatDate(startFin);
                      endFin = d.setDate(d.getDate());
                      formattedDateEnd = formatDate(endFin);
                      start = formattedDateStart;
                      end = formattedDateEnd;
                      break;
                  case 'Last Month':
                      startFin = d.setDate(d.getDate() - 30);
                      formattedDateStart = formatDate(startFin);
                      endFin = d.setDate(d.getDate());
                      formattedDateEnd = formatDate(endFin);
                      start = formattedDateStart;
                      end = formattedDateEnd;
                      break;
                  case 'Last Year':
                      startFin = d.setDate(d.getDate() - 365);
                      formattedDateStart = formatDate(startFin);
                      endFin = d.setDate(d.getDate());
                      formattedDateEnd = formatDate(endFin);
                      start = formattedDateStart;
                      end = formattedDateEnd;
                      break;

                  default:
                      startFin = d.setDate(d.getDate() - 7);
                      formattedDateStart = formatDate(startFin);
                      endFin = d.setDate(d.getDate());
                      formattedDateEnd = formatDate(endFin);
                      start = formattedDateStart;
                      end = formattedDateEnd;
                      document.getElementById('timeFrame').innerHTML = 'Time Frame: Last Week';
                      break;
              }
          }
          xhr.open('GET', '<?=$dataurl?>');

          xhr.onload = function() {

              if (xhr.status === 200) {

                  var response = JSON.parse(xhr.responseText);

                  var heading  = response.metadata.title;
                  for (var i in response.features) {
                      //  example ~ M 4.1 - 14km ESE of Alum Rock, California
                      var nam = response.features[i].properties.title;
                      var part = nam.split(',');
                      var sec = part[1];
                      if(sec === undefined) {
                          sec = part[0];
                      }
                      
                      //all features
                      var name = response.features[i].properties.title;
                      var type = response.features[i].properties.type;
                      var tsuna = response.features[i].properties.tsunami;
                      var magnitude = response.features[i].properties.mag;
                      var location = response.features[i].properties.place;
                      var place = response.features[i].geometry.coordinates;
                      var alert = response.features[i].properties.alert;
                      var pos = place[1];
                      var neg = place[0];
                      var unix_timestamp = response.features[i].properties.time;
                      var dateObj = new Date(unix_timestamp);
                      //foreach result add to earth as marker
                      var marker = WE.marker([pos, neg]).addTo(earth);
                      var tsun = "";
                      if(tsuna > 0) {tsun = 'Yes';} else {tsun = 'No';}

                      if(type === 'earthquake') {
                          // determine alert level
                          var b_message = "";
                          switch(alert) {
                            case "green":
                              b_message = "Moderate";
                              break;
                            case "yellow":
                              b_message = "Severe";
                              break;
                            case "orange":
                              b_message = "Danger";
                              break;
                            case "red":
                              b_message = "Extreme Danger";
                              break;
                          }
                          
                          marker.bindPopup(
                              '<div class="row" style="margin-bottom:0px;">'+
                              '  <div class="col s12">'+
                              '    <div class="card blue-grey darken-1">'+
                              '      <div class="card-content white-text">'+
                              '        <span class="card-title">'+name+'</span>'+
                              '        <p>Type: '+ type+'</p>'+
                              '        <p>Time: '+ dateObj.toDateString()+'</p>'+
                              '        <p>Location: '+location+'</p>'+
                              '        <p>Possible Tsunami: '+tsun+'</p>'+
                              '        <span>Alert Level</span>: <span style="background-color:'+alert+'">'+b_message+'</span>'+
                              '      </div>'+ 
                              '      <div class="card-action">'+
                              '        <a href="#"></a>'+
                              '        <a href="#"></a>'+
                              '      </div>'+
                              '    </div>'+
                              '  </div>'+
                              '</div>'
                          );
                      }
                  }
                  
                  document.getElementById('numberOfEvents').innerHTML = 'Number of Events: ' + response.features.length;
                  document.getElementById('heading').innerHTML = heading;
                  document.getElementById('timeFrame').innerHTML = heading;

              } else {
                  alert('Request failed.  Returned status of ' + xhr.status);
              }
          };
          xhr.send();
          
/** Start a simple rotation animation
 *          var before = null;
 *          requestAnimationFrame(function animate(now) {
 *          var c = earth.getPosition();
 *          var elapsed = before ? now - before : 0;
 *          before = now;
 *          earth.setCenter([c[0], c[1] + 0.1 * (elapsed / 500)]);
 *          requestAnimationFrame(animate);
 *          });
**/

      }
  </script>
  <link type="text/css" href="resources/css/style.css" rel="stylesheet"/>

  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="resources/css/materialize.min.css" media="screen,projection" />
  <title>Global Earthquake Monitor</title>
</head>

<body onload="initialize('default')">



<section class="z-depth-1">
  <div id="earth_div"></div>
</section>

<div style="position: fixed;z-index: 100000;top: 0;width: auto;" class="">
  <!-- Dropdown Structure -->
  <ul id="slide-out" class="side-nav">
    <li><div class="user-view">
      <div class="background">
        <img src="resources/images/wallpaper.jpg">
      </div>
      <a href="#!user"><img class="circle" src="http://via.placeholder.com/50x50"></a>
      <a href="#!name"><span class="white-text name">Username</span></a>
      <a href="#!email"><span class="white-text email">Email@example.com</span></a>
    </div></li>
    <li><a class="subheader">Find</a></li>
    <li>
      <ul class="collapsible collapsible-accordion">
        <li>
          <div class="collapsible-header">Significant<i class="material-icons right">arrow_drop_down</i></div>
          <div class="collapsible-body">
            <ul>
              <li><a href="?sigTime=pasthour">Past Hour</a></li>
              <li><a href="?sigTime=pastday">Past Day</a></li>
              <li><a href="?sigTime=pastweek">Past Week</a></li>
              <li><a href="?sigTime=pastmonth">Past Month</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </li>
    <li>
      <ul class="collapsible collapsible-accordion">
          <li>
              <div class="collapsible-header">M4.5+ Earthquakes<i class="material-icons right">arrow_drop_down</i></div>
              <div class="collapsible-body">
                  <ul>
                      <li><a href="?sigTime=pasthour&mag=4.5">Past Hour</a></li>
                      <li><a href="?sigTime=pastday&mag=4.5">Past Day</a></li>
                      <li><a href="?sigTime=pastweek&mag=4.5">Past Week</a></li>
                      <li><a href="?sigTime=pastmonth&mag=4.5">Past Month</a></li>
                  </ul>
              </div>
          </li>
      </ul>
    </li>

    <li>
      <ul class="collapsible collapsible-accordion">
          <li>
              <div class="collapsible-header">M2.5+ Earthquakes<i class="material-icons right">arrow_drop_down</i></div>
              <div class="collapsible-body">
                  <ul>
                      <li><a href="?sigTime=pasthour&mag=2.5">Past Hour</a></li>
                      <li><a href="?sigTime=pastday&mag=2.5">Past Day</a></li>
                      <li><a href="?sigTime=pastweek&mag=2.5">Past Week</a></li>
                      <li><a href="?sigTime=pastmonth&mag=2.5">Past Month</a></li>
                  </ul>
              </div>
          </li>
      </ul>
    </li>

    <li>
      <ul class="collapsible collapsible-accordion">
          <li>
              <div class="collapsible-header">M1.0+ Earthquakes<i class="material-icons right">arrow_drop_down</i></div>
              <div class="collapsible-body">
                  <ul>
                      <li><a href="?sigTime=pasthour&mag=1.0">Past Hour</a></li>
                      <li><a href="?sigTime=pastday&mag=1.0">Past Day</a></li>
                      <li><a href="?sigTime=pastweek&mag=1.0">Past Week</a></li>
                      <li><a href="?sigTime=pastmonth&mag=1.0">Past Month</a></li>
                  </ul>
              </div>
          </li>
      </ul>


      <li>
          <ul class="collapsible collapsible-accordion">
              <li>
                  <div class="collapsible-header">All Earthquakes<i class="material-icons right">arrow_drop_down</i></div>
                  <div class="collapsible-body">
                      <ul>
                          <li><a href="?sigTime=pasthour&mag=all">Past Hour</a></li>
                          <li><a href="?sigTime=pastday&mag=all">Past Day</a></li>
                          <li><a href="?sigTime=pastweek&mag=all">Past Week</a></li>
                          <li><a href="?sigTime=pastmonth&mag=all">Past Month</a></li>
                      </ul>
                  </div>
              </li>
          </ul>
      </li>

      <li><div class="divider"></div></li>
    <li><a class="subheader">More Information</a></li>
    <li><a href="#!" id="timeFrame"><i class="material-icons">time</i>Last Week</a></li>
    <li><a href="#!" id="numberOfEvents">NULL</a></li>
    <li><div class="divider"></div></li>
    <li><a class="subheader">Actions</a></li>
    <li><a class="waves-effect" onclick="getWinUrl()">See Data Tables</a></li>

  </ul>
  <a href="#" data-activates="slide-out" class="button-collapse hide-on-small-only"><i class="large material-icons">menu</i></a>
  <a href="#" data-activates="slide-out" class="button-collapse show-on-small hide-on-med-and-up"><i class="medium material-icons">menu</i></a>

</div>

<div style="z-index: 100000;position: absolute; bottom: 0; right: 0; width: auto; text-align:right;" class="right">
  <h5 style="color:#fff" id="heading">NULL</h5>
</div>
<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="resources/js/materialize.min.js"></script>

<script type="text/javascript">
    (function() {
        //
        $('.collapsible').collapsible();

        // Initialize collapse button
        $(".button-collapse").sideNav();


        $('.button-collapse').sideNav({
                menuWidth: 300, // Default is 300
                edge: 'left', // Choose the horizontal origin
                closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
                draggable: true, // Choose whether you can drag to open on touch screens,
                onOpen: function(el) {  }, // A function to be called when sideNav is opened
                onClose: function(el) {  }, // A function to be called when sideNav is closed
            }
        );

    })();
</script>
</body>

</html>
