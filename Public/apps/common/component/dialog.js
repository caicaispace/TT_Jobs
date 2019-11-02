/**
 * 弹窗组件
 */
define(['mu/baseView','bootstrap-dialog','mu/common'], function(baseView,BD,common) {
	var view = baseView.extend({

		events : {
			'blur [data-validation]' : 'validationOne',
			'click [data-custom-option]': 'customOption'
		},

		_initialize: function(options) {
			this._model     = options.model;		//	model
			this._id        = options.id || Functions.GUIDx4(2);	//	dialog id
			this._title     = options.title || 'title';	//	标题
			this._html      = options.html || 'html';	//	内容
			this._color     = options.color || 'default';  		//	标题颜色
			this._animate   = options.animate || false;  //	动画
			this._type      = options.type || 'null';	//	按钮类型
			this._cssClass  = options.cssClass || '';	//	class
			this._size      = options.size;	//	尺寸
			this._width     = options.width || 0;//	宽度 不可与尺寸同时使用
			this._max_hight = options.max_hight === false ? false : true;//	宽度 不可与尺寸同时使用
			this._top       = options.top || 0;//	顶部距离
			this._position  = options.position || 'default';//	位置
			this._scope     = options.scope; 		//	提交部分表单
			this._ctime     = options.ctime || 0; 	//	自动关闭时间 默认不自动关闭
			this._onShow    = options.onShow || ''; 	//	后置函数
			this._onShown   = options.onShown || ''; 	//	后置函数
			this._onHidden  = options.onHidden || ''; 	//	后置函数
			this._button1   = options.button1 || '关闭'; 	//	底部按钮名字1
			this._button2   = options.button2 || '提交'; 	//	底部按钮名字2
			this.render();
		},

		render: function() {

			// 	按钮
			// 	null 	无
			//	close	关闭
			//	submit	关闭 提交

			var _this   = this;
			var buttons = [], button = {};
			if (this._type === 'close' || this._type === 'submit') {
				button = {
                    label: this._button1,
                    hotkey: 27, // Esc.
                    action: function(dialogRef){
                        dialogRef.close();
                    }
	            };
				buttons.push(button);
				if (this._type === 'submit' || this._type === 2) {
	                button = {
		                label: this._button2,
		                // hotkey: 13, // Enter.
		                autospin: false, // 动画
		                cssClass: 'btn-primary',
		                action: function(dialogRef){
		                    _this.validationMore();
		                }
	                };
					buttons.push(button)
				}
			}

			// 尺寸
			var size = {
				'normal' : BD.SIZE_NORMAL,
				'small' : BD.SIZE_SMALL,
				'wide' : BD.SIZE_WIDE,
				'large' : BD.SIZE_LARGE
			};

			// 标题颜色
			var color = {
				'default': BD.TYPE_DEFAULT,
				'primary': BD.TYPE_PRIMARY,
				'info': BD.TYPE_INFO,
				'success': BD.TYPE_SUCCESS,
				'warning': BD.TYPE_WARNING,
				'danger': BD.TYPE_DANGER
			};

			switch(this._position){
				case 'left':
					this._cssClass = 'bs-extra-modal modal-left slide-left';
					break;
				case 'right':
					this._cssClass = 'bs-extra-modal modal-right slide-right';
					break;
			}

			var options = {
            	animate: this._animate,
            	closable: true,
				closeByBackdrop: false,
            	closeByKeyboard: false,
            	nl2br : false,
                title: this._title,
                size: size[this._size],
                type: color[this._color],
                cssClass: this._cssClass,
                message: this._html,
                buttons: buttons,
                onshow: _.bind(this.onShow, this), // 显示之前
                onshown: _.bind(this.onShown, this), // 后置函数
                onhide:_.bind(this.dialogOnHide, this),
                onhidden:_.bind(this.dialogOnhidden, this),
			};

			if (this._id) options['id'] = this._id;
            this.dialog = new BD(options);
			this.dialog.open();
			this.customPositionOption();
			this.autoClose();

			return this;
		},

		/* 自动关闭 */
		autoClose: function() {
			var _this = this;
			if (this._ctime !== 0) {
				setTimeout(function(){
					clearTimeout();
					_this.hide();
				},this._ctime);
			}
		},

		/* 自定义位置操作 */
		customPositionOption: function(){
			var _this = this;
			var button;
			if (this._position !== 'default') {
				if (this._width) {
					this.$el.width(this._width);
					this.dialog.$modal.width(this._width + 24);
					this.$el.find('.modal-content').width(this._width);
				}
				button = {
                    label: this._button1,
                    hotkey: 27, // Esc.
                    action: function(dialogRef){
						_this.dialog.$modal.addClass('unslide-'+_this._position)
						setTimeout(function(){
							_this.hide();
						}, 300);
                    }
	            };
				this.dialog.addButton(button);
				this.dialog.updateButtons();
			}
		},

		/* 自定义操作 */
		customOption: function(e){
			var $dom = $(e.currentTarget);
			var type = $dom.attr('data-custom-option');
			var param = {
				$dom: $dom,
				dialog: this
			};
			this.trigger(type, param);
		},

		/* 自定义触发器 */
		customTrigger: function(eve, type){
			var $dom = this.$el.find('[data-custom-option="'+eve+'"]');
			var param = {
				$dom: $dom,
				dialog: this
			};
			this.trigger(eve, param);
		},

		// 显示前
		onShow: function(){
			// 自定义宽度
			this.$el = this.dialog.getModalDialog();  //  modal 赋值给当前对象
			this._width > 0 && window.innerWidth > 768
				? this.$el.css('width', this._width)
				: '';

			this._top > 0 && window.innerWidth > 768
				? this.$el.css('marginTop', this._top)
				: '';

			if (this._cssClass.indexOf('bottom') > -1) {
				this.$el.css('marginTop', '10%');
			}

			if (this._max_hight) {
            	this.$el.find('.modal-body').addClass('modal-max-hight');
			}

			// 显示前回调
			if (this._onShow){
				this._onShow({$el: this.$el, model: this._model});
			}
		},

		// 后置函数
		onShown: function(){
			// 渲染完成回调
			if (typeof this._onShown === 'function'){
				this._onShown({$el: this.$el, model: this._model});
			}
		},

		// 单个验证
		validationOne : function(e){
			var $dom = $(e.currentTarget);
			var type = $dom[0].attributes['data-validation'].value;
			this.validatio($dom,type);
		},

		// 多个验证
		validationMore : function(){

			// KindEditor 同步
			if (typeof KindEditor !== 'undefined') {
				this.$el.find('[data-kindeditor="editor"]').each(function(){
					KindEditor.sync('#'+ $(this).attr('id'));
				});
			}

			var _this = this;
			var $doms = this.$('[data-validation]');
			var error = $doms.length;
			var status,type = false;

			if ($doms.length !== 0) {
				$doms.each(function(i){
					type   = this.attributes['data-validation'].value;
					status = _this.validatio($(this),type);
					if (status === true) error--;
				});
				if (error > 0 ) {
					common.note('error', '请正确填写数据！！！');
					return false;
				}
			}
			this.formSubmit();
		},

		// 验证
		validatio: function(that,type){

			var
				_opt,
				regex,
				err_func,
				suc_func,
				len,
				len_min,
				len_max,
				$dom_pd,
				val,
				pwd,
				isVal;

			_opt = {
				prompt_dom : '[data-validation-prompt="prompt"]', // 错误提示 dom 标识
				prompt_types : {
					must: {
						regex: /.+/,
						msg:  '必填'
					},
					email: {
						regex: /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
						msg:  '请填写正确的邮箱'
					},
					phone: {
						regex: /1[0-9]{10}/,
						msg:  '请填写正确的手机号'
					},
					qq: {
						regex: /[1-9]\d{4,}/,
						msg:  '请填写正确的QQ号'
					},
					length: {
						regex: /^\S{3,6}$/,
						msg:  '请填写 3~6 个字符'
					},
					number: {
						regex: /^\d+$/,
						msg:  '只可填写数字'
					},
					float: {
						regex: /^[0-9]+([.]{1}[0-9]{1,2})?$/,
						msg:  '请填写整数或 2 位小数'
					},
					date: {
						regex: /^(\d{4})-(\d{2})-(\d{2})$/,
						msg:  '请填写正确日期格式'
					},
					idcard: {
						regex: /\d{15}|\d{18}/,
						msg:  '请填写正确身份证号'
					},
					password: {
						regex: /[0-9|A-Z|a-z]{6,16}/,
						msg:  '密码为6~16个数字与字母组合的字符'
					},
					repassword: {
						regex: '',
						msg:  '两次密码填写不一致'
					}
				},
				prompt_msg : function(type){
					return '<label class="form-validation-prompt error" data-validation-prompt="prompt">'
							+this.prompt_types[type].msg+ '</label>';
				}
			};

			val = $.trim(that[0].value);			// input 值
			$dom_pd = that.next(_opt.prompt_dom);	// 误提示 dom

			// 错误
			err_func = _.bind(function(type){
				if ($dom_pd.length < 1)
					that.css({borderColor:'#a94442'}).after(_opt.prompt_msg( type)); // 错误提示
				return false;
			},that);

			// 正确
			suc_func = _.bind(function(){
				$dom_pd.remove();
				that.css({borderColor:'#3c763d'});
				return true;
			},that);

			// 密码验证
			if (type === 'repassword') {
				pwd  = this.$el.find('[data-validation=password]').val();
				return val != pwd || val == '' || val == null
					? err_func('repassword')
					: suc_func();
			}

			// 整数或浮点数
			// data-validation="float_2" 匹配整数或两位小数
			if (type.indexOf('float') > -1) {
				len = type.split('_')[1];
				_opt.prompt_types.float.regex = new RegExp('^[0-9]+([.]{1}[0-9]{1,'+len+'})?$');
				_opt.prompt_types.float.msg   = '请输入整数或'+len+'位小数';
				type = 'float';
			}

			// 日期
			if (type.indexOf('date') > -1) {
				isVal = setInterval(function(){
					if (that.val() != '' && that.val() != undefined) {
						that.blur();
						clearInterval(isVal);
					}
				},500);
			}

			// 字符长度
			// data-validation="len_3_6" 匹配 3~6 个字符
			if (type.indexOf('len') > -1) {
				len = type.split('_');
				len_min = len[1];
				len_max = len[2];
				_opt.prompt_types.length.regex = new RegExp('^\\S{'+len_min+','+len_max+'}$');
				_opt.prompt_types.length.msg   = '请输入 '+len_min+'~'+len_max+' 个字符';
				type = 'length';
			}

			// 正则匹配
			regex = _opt.prompt_types[type].regex;
			return !regex.test(val)
				? err_func(type)
				: suc_func();
		},

		// 表单提交
		formSubmit: function() {
			var data = this.$el.find('form').serializeObject();
			this.trigger('submit', {
				dialog: this,
				data: data,
				model: this._model
			});
		},

		// 隐藏
		hide: function() {
			this.dialog.close();
		},

		// 隐藏之前
		dialogOnHide: function() {

		},

		// 隐藏之后
		dialogOnhidden: function() {
			this.remove().off();
			if (typeof this._onHidden == 'function'){
				this._onHidden({$el: this.$el, model: this._model});
			}
		}
	});
	return view;
});
