(function() {
    sendPacket(AMF.stringify({greeting: 'hello!'}), '/greeting', function(e) {
       console.log(AMF.parse(e.currentTarget.responseText));
    });
})();