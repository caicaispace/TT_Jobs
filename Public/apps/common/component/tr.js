/**
 * tr 组件
 */
define(['backbone'], function(Backbone) {
	var view = Backbone.View.extend({

		tagName: 'tr',
		events: {
			'click [data-id]': 'option',
			'click [data-tr="checked"]': 'isChecked',
		},

		initialize: function(options) {
			this.options = options;
			this.fn = options.fn;
			this.model.on('change', this.change, this);
			this.model.on('remove', this.destroy, this);
		},

		render: function() {

			var data     = this.model.toJSON();
			var _fields  = this.options.fields || '';
			var _button  = this.options.button || '';
			var _replace = this.options.replace || '';
			var _isCheck = this.options.isCheck || '';
			var _align   = this.options.align || '';
			var fn       = this.fn || '';
			var tpl      = _isCheck
								? '<td class="text-center" ><input data-tr="checked" type="checkbox" /> </td>'
								: '';
				// tpl += '<td>'+ this.options.i +'</td>';

			var align,k,j,keys,names,merge='';
			for (k in _fields) {

				// td 合并
				if (k.indexOf(',') > -1) {
					keys = k.split(',');
					names = _fields[k].split('/');
					for(j in keys){
						merge += names[j]+': '+data[keys[j]]+'<br/>';
					}
					tpl += '<td class="text-center">' + merge + '</td>';
					continue;
				}

				// td 替换
				if (_replace && typeof(_replace[k]) === 'string') {
					tpl += '<td class="text-center">' + _replace[k] + '</td>';
					continue;
				}

				// 文本替换
				if (_replace && _replace[k] !== void 0){
					data[k] = _replace[k][data[k]];
				}

				// 回掉处理
				if (fn && _.isArray(fn)) {
					for (var f in fn)
						if (fn[f].field === k)
							data[k] = this.functions(fn[f].fn, data[k]);
				} else if (fn && fn.field === k) {
					data[k] = this.functions(fn.fn, data[k]);
				}

				// 文本对齐
				if (typeof _align === 'object') {
					align = _align[k] === void 0
							? 'class="text-center"'
							: 'class="text-'+ _align[k] +'"';

					tpl += '<td '+ align +' ><%= ' + k + ' || "--" %></td>';
				}else{
					tpl += '<td class="text-'+ _align +'" ><%= ' + k + ' || "--" %></td>';
				}
			}

			// 操作按钮
			if (_button) {
				_button = _.template(_button) (this.model.toJSON());
				tpl += '<td class="text-'+ _align +'" >' + _button + '</td>';
				// tpl += '<td class="text-'+ _align +' table-action" >' + _button + '</td>';
			}

			// 是否被选择
			if (_isCheck && typeof data.__isChecked__ === 'undefined')
				this.model.set('__isChecked__' , false, {silent: true});

			this.$el.html(_.template(tpl) (data));
			return this;
		},

		/* 数据处理函数 */
		functions: function(type, data) {

			var result = '--';
			var types  = type.split('|');
			var fn     = types[0];
			var param  = types[1] || '';

			switch (fn) {

				// 日期函数
				case 'date':
					if (data != 0) {
						result = new Date(data * 1000).format( param || 'yyyy-MM-dd' );
					}
					break;

				// 字符串截取
				case 'cutstr':
					if (_.isFunction(__f.cutstr)) {
						result = __f.cutstr(data, param);
					}
					break;

				default:
					result = '--';
			}
			return result;
		},

		/* 操作 */
		option: function(e) {
			var $ele = $(e.currentTarget);
			var options = $ele.attr('data-id');
			var param = {
				$ele: $ele,
				model: this.model
			};
			this.trigger('tr', options, param);
		},

		/* 选中 */
		isChecked: function(e){
			this.model.__isChecked__ = e.currentTarget.checked;
		},

		/* 状态改变动画 */
		change: function() {
			this.render();
			this.$el.css({background: "#4cae4c" ,opacity: 0.5})
					.animate({opacity: 1}, 700, function() {
						$(this).css({background: "", color: ""});
					});
		},

		/* 移除 */
		destroy: function() {
			this.$el.css({background: "#C9302C"})
					.animate({opacity: 'toggle'}, 1000, _.bind(function () {
						this.remove();
					}, this));
		}
	});
	return view;
});
