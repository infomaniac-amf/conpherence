var BaseModel = require('./Base');

var Speaker = function (params) {
    BaseModel.call(this, params);
};

Speaker.prototype = new BaseModel();
Speaker.prototype.constructor = Speaker;
Speaker.prototype._classMapping = 'Conpherence\\Entities\\Speaker';

AMF.registerClassAlias('Conpherence\\Entities\\Speaker', Speaker);

module.exports = BaseModel;