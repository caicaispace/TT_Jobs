define(['backbone','m/emptyModel', 'notification'],function(Backbone,model, notification){
	var Collection = Backbone.Collection.extend({
	    model : model,
	    url : '',
	    parse : function(result,options){
			this.__isCheckedAll__ = false; // 是否全选 model
			this._option = result.options || {};
			this._page   = result.page || {};
			options.uniqueId = this._option.uniqueId ? this._option.uniqueId : 'id';
			return result.list;
		},

		/**
		 * 多选删除 model
		 * @param  {arr}   collection 数据集合
		 * @param  {Function} callback
		 */
		deletes: function(collection , callback){

			var cb = callback != void 0 && typeof callback == 'function'
							? callback
							: function(){};

			var models = [];
			collection.each(function(model){
				if (model.__isChecked__ == true) models.push(model);
			})

			if (models.length <= 0) {
    			notification({type:'error', msg: '您还没有选择数据！'});
				return;
			};

    		notification({type:'success', msg: '删除成功'});

			setTimeout(function(){
				models.pop().destroy();
				models.length > 0
					? setTimeout(arguments.callee, 10)
					: cb();
			},10);


		},

		/**
		 * 集合数量限制
		 * @param  {arr}   collection
		 * @param  {int}   limit
		 * @param  {Function} callback
		 */
		limit: function(collection, limit, callback){

			var cb = typeof callback != 'undefiend' && typeof callback == 'function'
							? callback
							: function(){};

			setTimeout(function(){
				collection.pop();
				limit --;
				limit>0
					? setTimeout(arguments.callee, 10)
					: cb();
			},10);
		}

	});
	return Collection;
});
