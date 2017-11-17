<?php
error_reporting(0);
    if(!isset($_REQUEST['sigTime'])) {
        $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_week.geojson';
    }
    $url = $_REQUEST['sigTime'];
    $mag = $_REQUEST['mag'];
    $dataurl = "";
    if(!isset($mag)) {
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
    } else {
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
                $dataurl = 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/4.5_hour.geojson';
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

      function gup() {
          /**GUP: GET URL PARAMETERS**/
          function gup() {
              var qs = document.location.search;
              qs = qs.split('+').join(' ');
              var params = {}, tokens, re = /[?&]?([^=]+)=([^&]*)/g;
              while (tokens = re.exec(qs))
                  params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
              return params;
          }
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
              switch (time) {
                  case 'Last Week':
                      var d = new Date();
                      var startFin = d.setDate(d.getDate() - 7);
                      var formattedDateStart = formatDate(startFin);
                      var d = new Date();
                      var endFin = d.setDate(d.getDate());
                      var formattedDateEnd = formatDate(endFin);
                      var start = formattedDateStart;
                      var end = formattedDateEnd;
                      break;
                  case 'Last Month':
                      var d = new Date();
                      var startFin = d.setDate(d.getDate() - 30);
                      var formattedDateStart = formatDate(startFin);
                      var d = new Date();
                      var endFin = d.setDate(d.getDate());
                      var formattedDateEnd = formatDate(endFin);
                      var start = formattedDateStart;
                      var end = formattedDateEnd;
                      break;

                  case 'Last Year':
                      var d = new Date();
                      var startFin = d.setDate(d.getDate() - 365);
                      var formattedDateStart = formatDate(startFin);
                      var d = new Date();
                      var endFin = d.setDate(d.getDate());
                      var formattedDateEnd = formatDate(endFin);
                      var start = formattedDateStart;
                      var end = formattedDateEnd;
                      break;

                  default:
                      var d = new Date();
                      var startFin = d.setDate(d.getDate() - 7);
                      var formattedDateStart = formatDate(startFin);
                      var d = new Date();
                      var endFin = d.setDate(d.getDate());
                      var formattedDateEnd = formatDate(endFin);
                      var start = formattedDateStart;
                      var end = formattedDateEnd;
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
                      debugger;
                      //M 4.1 - 14km ESE of Alum Rock, California
                      var nam = response.features[i].properties.title;
                      var part = nam.split(',');
                      var sec = part[1];
                      if(sec === undefined) {
                          var sec = part[0];
                      }
                      function getData() {
                          //get links for each marker
                          var getlinks = new XMLHttpRequest();
                          getlinks.open('GET', 'scraper.php?search='+sec);
                          getlinks.send();
                          getlinks.onload = function() {
                              if (getlinks.status === 200) {
                                  var comeback = (getlinks.response);
                              }

                          };
                          return getlinks.response;
                          //get links for each marker
                      }
                      //all features
                      var type = response.features[i].properties.type;
                      var tsuna = response.features[i].properties.tsunami;
                      var magnitude = response.features[i].properties.mag;
                      var place = response.features[i].geometry.coordinates;
                      var pos = place[1];
                      var neg = place[0];
                      var unix_timestamp = response.features[i].properties.time
                      var dateObj = new Date(unix_timestamp);
                      //foreach result add to earth as marker
                      var marker = WE.marker([pos, neg]).addTo(earth);
                      if(tsuna > 0) {var tsun = 'Yes'} else {tsun = 'No'}

                      if(type === 'earthquake') {

                          marker.bindPopup(
                              '<h6>' + name + '</h6><br />' +
                              '<h6>Type:' + type + '</h6>' +
                              '<b>Time: ' + dateObj + '</b><br/>' +
                              //                        '<button data-target="modal1" class="btn modal-trigger" onclick="openModal(name);">View More</button>'+
                              '  <ul class="collapsible" data-collapsible="accordion">' +
                              '    <li>' +
                              '      <div class="collapsible-header"><i class="material-icons">filter_drama</i>More</div>' +
                              '      <div class="collapsible-body">' +
                              '         <span>Tsunami Possible: '+tsun+'</span>' + '<br/>' +
                              '         <span>Links: </span>' + getlinks +
                              '      </div>' +
                              '    </li>' +
                              '  </ul>'
                          );
                      }



                  }

                  init();
                  document.getElementById('numberOfEvents').innerHTML = 'Number of Events: ' + response.features.length;
                  document.getElementById('heading').innerHTML = heading;
                  document.getElementById('timeFrame').innerHTML = heading

              } else {
                  alert('Request failed.  Returned status of ' + xhr.status);
              }
          };
          xhr.send();

          //Start a simple rotation animation
//          var before = null;
//          requestAnimationFrame(function animate(now) {
//              var c = earth.getPosition();
//              var elapsed = before ? now - before : 0;
//              before = now;
//              earth.setCenter([c[0], c[1] + 0.1 * (elapsed / 500)]);
//              requestAnimationFrame(animate);
//          });


      }
  </script>
  <style type="text/css">
    html,
    body {
      padding: 0;
      margin: 0;
      background: #000;
    }

    #earth_div {
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      position: absolute !important;
      background-image: -webkit-gradient( linear,
      left bottom,
      left top,
      color-stop(0, rgb(253, 253, 253)),
      color-stop(0.15, rgb(253, 253, 253)),
      color-stop(0.53, rgb(223, 223, 223)),
      color-stop(0.56, rgb(255, 255, 255)),
      color-stop(1, rgb(253, 253, 253)));
      background-image: -moz-linear-gradient( center bottom,
      rgb(253, 253, 253) 0%,
      rgb(253, 253, 253) 15%,
      rgb(223, 223, 223) 53%,
      rgb(255, 255, 255) 56%,
      rgb(253, 253, 253) 100%);
    }
  </style>

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
    function init() {

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

    }
</script>
</body>

</html>
