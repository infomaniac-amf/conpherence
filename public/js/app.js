(function() {
    get('/amf/speakers', function() {
        var s = Date.now();
        console.log(AMF.parse(this.responseText));
        console.log((Date.now() - s) + 'ms');
    });
})();