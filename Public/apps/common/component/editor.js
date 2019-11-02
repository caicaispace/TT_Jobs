/**
 * kindEditor 编辑器
 */
define(['backbone','mu/common','kindeditor'],function(Backbone,common,kindeditor){
	var view = Backbone.View.extend({

		events: {
			'click #kindeditor-reload': 'render'
		},

		KE:'',

		initialize:function(options){
			this._name         = options.name || 'content';  // textarea name
			this._contents     = options.contents || ''; // 默认文本
			this._type         = options.type || 'default';	// 样式 默认为默认样式
			this._readonlyMode = options.readonlyMode || false;	// 是否为只读
			this._width        = options.width || '100%';	// 宽度
			this._height       = options.height || 250;	// 高度
			this._must         = options.must || false;	// 是否必填
			this._word_count   = options.word_count || false; // 是否需要字数统计
			this._word_limit   = options.word_limit || 1000;	// 字数限制
			this.render();
		},

		render : function(){

			this.$el.html(this.tpl(0));

			var _this = this,
				dom_id = 'kindeditor-'+this.cid,
				ke_conf,
				itime,
				$dom_word_count;

			// 默认样式
			ke_conf = {
				height: this._height,
				readonlyMode: this._readonlyMode,
				resizeType : 2,
				// filterMode: false,
				themeType : 'simple',
				uploadJson : '/admin/common?__act=fileUpload&ke=1', // 图片上传
			};

			// 简单样式
			if (this._type === 'simple') {
				ke_conf.allowPreviewEmoticons = false;
				ke_conf.allowImageUpload = false;
				ke_conf.items = [
					'fontname', 'fontsize', '|',
					'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat', '|',
					'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist','insertunorderedlist', '|',
					'emoticons','source', 'multiimage','fullscreen'
				];
			}

			// 字数统计
			if (this._word_count === true){
				this.$el.append(this.tpl(1));
				$dom_word_count = this.$el.find('[data-kindeditor=word-count-'+this.cid+']');
				ke_conf.afterChange = function (){
					$dom_word_count.html(this.count());
				};
			}

			__f.isEleRenderDone(
				this.$el,
				function() {
					_this.KE = KindEditor.create('#' + dom_id, ke_conf)
				},
				300,
				function() {
					_this.$el.html(_this.tpl(2))
				}
			);

			return this;
		},

		tpl : function(){
			var must = this._must ? ' data-validation="must"' : '';
			var arr = [
				'<textarea id="kindeditor-'+this.cid+'"'
					+' name="'+this._name+'"'
					+' data-kindeditor="editor"'
					+' style="width:'+this._width+';height:'+this._height+'px"'
					+must
				+'">'
					+this._contents
				+'</textarea>',
				'字数统计 ( <span data-kindeditor="word-count-'+this.cid+'"></span> / <span class="" >'+this._word_limit+' )</span>',
				'<center><button class="btn btn-lightblue" id="kindeditor-reload">加载编辑器</button></center>'
			];
			return arr[arguments[0]];
		}

	});
	return view;
});
