var BaseModel = require('./Base');

var Image = function (params) {
    BaseModel.call(this, params);
};

Image.prototype = new BaseModel();
Image.prototype.constructor = Image;
Image.prototype._classMapping = 'Conpherence\\Entities\\Image';

AMF.registerClassAlias('Conpherence\\Entities\\Image', Image);

module.exports = Image;