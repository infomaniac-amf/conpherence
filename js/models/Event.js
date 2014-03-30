var BaseModel = require('./Base');

var Event = function (params) {
    BaseModel.call(this, params);
};

Event.prototype = new BaseModel();
Event.prototype.constructor = Event;
Event.prototype._classMapping = 'Conpherence\\Entities\\Event';

AMF.registerClassAlias('Conpherence\\Entities\\Event', Event);

module.exports = Event;