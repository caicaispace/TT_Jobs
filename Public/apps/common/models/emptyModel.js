define(['backbone'],function(Backbone){
	var model = Backbone.Model.extend({
	    urlRoot : '',
	    parse : function(data,options){
	    	this.__isChecked__ = false; // 是否被选中
			if (typeof options.collection === 'undefined'){
				//options.xhr.responseJSON.options
				if (this.idAttribute === 'id' && data.options.uniqueId )
					this.idAttribute = data.options.uniqueId || 'id';
				return data.info || {};
			}else {
				this.idAttribute = options.uniqueId || 'id';
				return data;
			}
	    }
	});
	return model;
});
