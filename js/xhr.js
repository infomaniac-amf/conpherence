if (!XMLHttpRequest.prototype.sendAsBinary) {
    XMLHttpRequest.prototype.sendAsBinary = function (data) {
        var numBytes = data.length, byteArray = new Uint8Array(numBytes);
        for (var i = 0; i < numBytes; i++) {
            byteArray[i] = data.charCodeAt(i) & 0xff;
        }

        this.send(byteArray.buffer);
    };
}

function get(uri, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', uri, true);
    xhr.overrideMimeType('application/x-amf; charset=x-user-defined');
    xhr.onreadystatechange = function (event) {
        if (event.currentTarget.readyState == XMLHttpRequest.DONE) {
            var data = AMF.parse(this.responseText);
            callback.apply(this, [data]);
        }
    };

    xhr.send();
}

function post(uri, packet, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', uri, true);
    xhr.setRequestHeader('Content-Type', 'application/x-amf');
    xhr.overrideMimeType('application/x-amf; charset:x-user-defined');
    xhr.onreadystatechange = function (event) {
        if (event.currentTarget.readyState == XMLHttpRequest.DONE) {
            var data = AMF.parse(this.responseText);
            callback.apply(this, [data]);
        }
    };

    xhr.sendAsBinary(packet);
}

module.exports = {
    getAMF: get,
    postAMF: post
};