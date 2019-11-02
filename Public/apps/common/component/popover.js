define(['backbone','mu/cache','moment'],function(Backbone,cache,moment){
	var view = Backbone.View.extend({

		tagName : 'button',

		className: 'btn btn-lg btn-danger',

		events : {
			'click [data-id="show-add-list"]': 'list',
			'click [data-id="select-add"]': 'add',
			'click [data-id="select-reduce"]': 'reduce',
		},

		_doms: {
			data_input: '',
			default_list: '',
			add_list: ''
		},

		initialize : function(options){

			this.$el.attr('data-toggle', 'popover');
			this.$el.attr('data-content', "And here's some amazing content. It's very engaging. Right?");
			this.$el.attr('title', 'Popover title');
			this.$el.html('点我弹出/隐藏弹出框');

			this.options       = options;
			this._type         = options.type || 'text'; // text or img
			this._must         = options.must || false; // text or img
			this._input_name   = options.input_name || 'id'; // text or img
			this._data         = options.data ? options.data.slice() : []; // 原有数据 浅拷贝
			this._default_data = options.default_data ? options.default_data.slice() : []; // 默认数据 浅拷贝
			_.bindAll(this,'init');
			cache.gets([
				{src:'/apps/common/template/popover.html'}
			],this.init);
		},

		init : function(results){
			this.tpl = results[0].split('||&&||');
			this.render();
			this.cache();
			this.defaultList();
			this.inputChange();
		},

		render : function(date){
			var dData = this._default_data;
			var data  = this._data;
			var diff  = this._data = Functions.diff(dData, data);
			this.$el.html(_.template(this.tpl[0])());
		},

		/* 缓存 */
		cache: function(){
			var data_input   = this.$('[data-id=data-input]');
			data_input[0].name = this._input_name;
			if (this._must)
				data_input.attr('data-validation','must');
		},

		/* 默认数据列表 */
		defaultList: function(){
			var html = _.template(this.tpl[1]) ({default_data: this._default_data});
			this.$('[data-id=default-list]').html(html);
		},

		/* 全部数据 */
		list: function(e){
			var html = _.template(this.tpl[2]) ({data: this._data});
			this.$('[data-id=add-list]').html( html );
		},

		/* 添加 */
		add: function(e){
			
			var 
				$dom = $(e.currentTarget),
				no   = $dom.attr('data-list-no'),
				data = this._data[no];

			delete this._data[no];
			// this._data.baoremove(no);
			this._default_data.push( data );
			this.inputChange();
			$dom.remove();
			this.defaultList();

		},

		/* 去除 */
		reduce: function(e){
			var 
				$dom = $(e.currentTarget),
				no   = $dom.attr('data-list-no'),
				data = this._default_data[no];

			delete this._default_data[no];
			this._data.push( data );
			this.inputChange();
			$dom.remove();
			this.list();
		},

		/* 表单数据更改 */
		inputChange: function(){
			var values = Functions.arrObjVal(this._default_data,'id');
			this.$('[data-id=data-input]')[0].value = values.join(',');
		},

		hide: function(){
			this.remove();
		}

	});
	return view;
});