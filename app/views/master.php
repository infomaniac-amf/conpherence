<!DOCTYPE html>
<html ng-app="conpherence">
<head>
    <meta charset="utf-8">

    <script src="/bower_components/infomaniac-amf.js/dist/amf.js" type="text/javascript"></script>
    <script src="/bower_components/angular/angular.min.js" type="text/javascript"></script>

    <script src="/js/xhr.js" type="text/javascript"></script>
    <script src="/js/models/Base.js" type="text/javascript"></script>
    <script src="/js/models/Speaker.js" type="text/javascript"></script>
    <script src="/js/models/Session.js" type="text/javascript"></script>
    <script src="/js/models/Event.js" type="text/javascript"></script>
    <script src="/js/app.js" type="text/javascript"></script>

    <link href="/css/style.css" type="text/css" rel="stylesheet">
    <title></title>
</head>
<body>
    <div ng-controller="AppCtrl">
        <div class="speaker-box" ng-repeat="speaker in speakers">
            <h1>{{ speaker.name }} <span class="twitter">{{ speaker.twitter }}</span>
                <img class="country-flag" src="data:image/png;base64,{{ speaker.flag.getData() }}">
            </h1>

            <img class="country-flag" src="data:image/jpeg;base64,{{ speaker.image.getData() }}">

            <p class="bio">{{ speaker.bio }}</p>

            <p>{{ speaker.name }} has {{ speaker.sessions.length }} sessions to present</p>
        </div>
    </div>

    <form action="/speaker" id="createSpeaker">
        <input type="text" name="name">
        <input type="text" name="twitter">
        <input type="text" name="country">
        <textarea rows="3" name="bio"></textarea>

        <button type="submit">Submit</button>
    </form>
</body>
</html>