/**
 * 数据模型
 * @param  {[type]} Backbone){	var model         [description]
 * @param  {[type]} validate  
 * @return {[type]}                 [description]
 */
define(['backbone'],function(Backbone){
	var model = Backbone.Model.extend({
	    urlRoot : '/gaveAddress',
	    parse : function(data){
	      this.id = data.dat.bid || null;
	      return data.dat;
	    },
	    validate : function(attrs,options){
	    }
	    // Domain-specific methods go here
	});
	return model;
});