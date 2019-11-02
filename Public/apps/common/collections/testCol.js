/**
 * 数据集合
 * @param  {[type]} Backbone     [description]
 * @param  {[type]} model){	var Collection    [description]
 * @return {[type]}              [description]
 */
define(['backbone','m/testModel'],function(Backbone,model){
	var Collection = Backbone.Collection.extend({
	    model : model,
	    url : '/publish',
	    parse : function(reply,options){
			this._pageConf = reply.option;
			return reply.list;
		}
	    
	});
	return Collection;
});