<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title>Global Earthquake Monitor | Welcome</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="resources/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="resources/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body style="margin: 0; padding: 0; background: #ffffff !important; background-attachment: fixed; background-size: cover;">
<nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">GEM</a>
        <ul class="right hide-on-med-and-down">
            <li><a href="earth.php"><i class="material-icons left">explore</i>Go to Earth</a></li>
            <li><a href="help.html"><i class="material-icons left">help</i>Help</a></li>
            <li><a href="login.html" class="waves-effect waves-light btn blue">Login</a></li>
            <li><a href="signup.html" class="waves-effect waves-light btn orange">Sign Up</a></li>
        </ul>

        <ul id="nav-mobile" class="side-nav">
            <li><a href="earth.php"><i class="material-icons left">explore</i>Go to Earth</a></li>
            <li><a href="help.html"><i class="material-icons left">help</i>Help</a></li>
            <li><a href="login.html" class="waves-effect waves-light btn">Login</a></li>
            <li><a href="signup.html" class="waves-effect waves-light btn orange">Sign Up</a></li>
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
</nav>
<div class="section no-pad-bot" id="index-banner">
    <div class="container">
        <br><br>
        <h1 class="header center orange-text">Global Earthquake Monitor</h1>
        <div class="row center">
            <h5 class="header col s12 light">Get visual access to USGS information, represented on a virtual Earth</h5>
        </div>
        <div class="row center">
            <a href="earth.php" id="download-button" class="btn-large waves-effect waves-light orange">Go There</a>
        </div>
        <br><br>

    </div>
</div>

<video style="width:100%" autoplay="" loop="" id="video-background" muted="" plays-inline="">
  <h1></h1>
  <source src="https://player.vimeo.com/external/158148793.hd.mp4?s=8e8741dbee251d5c35a759718d4b0976fbf38b6f&amp;profile_id=119&amp;oauth2_token_id=57447761" type="video/mp4">
</video>

<!--  Scripts-->
<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="resources/js/materialize.js"></script>
<script src="resources/js/init.js"></script>

</body>
</html>
