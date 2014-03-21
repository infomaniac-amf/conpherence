if (!XMLHttpRequest.prototype.sendAsBinary) {
    XMLHttpRequest.prototype.sendAsBinary = function (data) {
        var numBytes = data.length, byteArray = new Uint8Array(numBytes);
        for (var nIdx = 0; nIdx < numBytes; nIdx++) {
            byteArray[nIdx] = data.charCodeAt(nIdx) & 0xff;
        }

        this.send(byteArray.buffer);
    };
}

function sendPacket(packet, uri, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', uri, true);
    xhr.setRequestHeader('Content-Type', 'application/x-amf');
    xhr.overrideMimeType('application/x-amf; charset:x-user-defined');
    xhr.onreadystatechange = function (event) {
        if (event.currentTarget.readyState == XMLHttpRequest.DONE) {
            callback.apply(this, [event, xhr]);
        }
    };

    xhr.sendAsBinary(packet);
}