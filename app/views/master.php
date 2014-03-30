<!DOCTYPE html>
<html ng-app="conpherence">
<head>
    <meta charset="utf-8">

    <script src="/bower_components/infomaniac-amf.js/dist/amf.js" type="text/javascript"></script>
    <script src="/bower_components/angular/angular.min.js" type="text/javascript"></script>
    <script src="/bower_components/angular-ui-router/release/angular-ui-router.min.js" type="text/javascript"></script>
    <script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>

    <script src="/js/app.js" type="text/javascript"></script>

    <link href="/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="/css/style.css" type="text/css" rel="stylesheet">
    <title>Conpherence</title>
</head>
<body>
<div ng-controller="AppCtrl">

    <div class="container">
        <div class="navbar navbar-default">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Conpherence</a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="https://github.com/infomaniac-amf">Infomaniac-AMF</a></li>
                </ul>
            </div>
        </div>

        <div ui-view>
        </div>
    </div>

    <!--<div class="speaker-box" ng-repeat="speaker in speakers">
        <h1>{{ speaker.name }} <span class="twitter">{{ speaker.twitter }}</span>
            <img class="country-flag" src="data:image/png;base64,{{ speaker.flag.getData() }}">
        </h1>

        <img class="country-flag" src="data:image/jpeg;base64,{{ speaker.image.getData() }}">

        <p class="bio">{{ speaker.bio }}</p>

        <p>{{ speaker.name }} has {{ speaker.sessions.length }} sessions to present</p>
    </div>-->
</div>
</body>
</html>