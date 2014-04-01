<!DOCTYPE html>
<html ng-app="conpherence">
<head>
    <meta charset="utf-8">

    <script src="/bower_components/infomaniac-amf.js/dist/amf.js" type="text/javascript"></script>
    <script src="/bower_components/angular/angular.min.js" type="text/javascript"></script>
    <script src="/bower_components/angular-ui-router/release/angular-ui-router.min.js" type="text/javascript"></script>
    <script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="/bower_components/moment/min/moment.min.js" type="text/javascript"></script>

    <script src="/js/app.js" type="text/javascript"></script>

    <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" type="text/css" rel="stylesheet">
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
</div>
</body>

<!--<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>-->
</html>