var BaseModel = require('./Base');

var Session = function (params) {
    BaseModel.call(this, params);
};

Session.prototype = new BaseModel();
Session.prototype.constructor = Session;
Session.prototype._classMapping = 'Conpherence\\Entities\\Session';

AMF.registerClassAlias('Conpherence\\Entities\\Session', Session);

module.exports = Session;