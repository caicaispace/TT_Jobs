
define(['backbone'],function(Backbone){
	var model = Backbone.Model.extend({
	    urlRoot : '/gaveAddress',
	    parse : function(resp){
	      this.id = data.dat.bid || null;
	      return data.dat;
	    },
	    validate : function(attrs,options){    
	    }
	    // Domain-specific methods go here
	});
	return model;
});