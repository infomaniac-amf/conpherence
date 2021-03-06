var BaseModel = function(data) {
    if(data) {
        this.importData(data);
    }
};

BaseModel.prototype = {
    exportData: function() {
        var exported = {};
        for(var key in this) {
            var value = this[key];

            if(typeof value == 'function') {
                continue;
            }

            exported[key] = value;
        }

        return exported;
    },

    importData: function(data) {
        if(!data) {
            return;
        }

        for(var key in data) {
            var value = data[key];
            this[key] = value;
        }
    }
};

module.exports = BaseModel;