/**
 * 视图基础库
 */
define(['backbone'],function(Backbone){

	/*
		110000. 系统级别
		119000. 系统级别 error
		120000. 用户
		129000. 用户 error
		130000. 订单
		139000. 订单 error
		140000. 文章
		149000. 文章 error
		150000. 产品
		159000. 产品 error
	 */

	var titles = {
		'danger': '注意',
		'info': '提示'
	};

	var contents = {
		'129019': '此名称一旦保存将无法修改',
		'129020': '此名称必须是唯一的，不能和现有的组名重复',
		'150010': '此名称必须是唯一的，一旦保存后，将无法更改，建议采取认知度较高的标准名称',
		'150011': '请不要重复填写上述所在地区的相同信息',
		'150012': '输入数据后敲回车即可添加',
		'150013': '此名称必须是唯一的，一旦保存后，将无法更改，建议采取认知度较高的标准名称',
		'150014': '请按对接平台要求的规则输入产品识别编码',
		'150015': '默认一定分钟后不支付自动取消（默认 2 x 60，最短 5 分钟，最大值不可超过 21600 分钟）',
		'150016': '填 " 0 " 为无限制',
		'150017': '百分比取值范围：0-100，需退款金额 x 百分比',
		'150018': '',
		'150019': '',
		'150020': '',
		'150021': '',
		'150022': '',
		'150023': '',
		'150024': '',
		'150025': '',
		'150026': '',
		'140100': '请填写http://',
		'140200': '请不要填写http://'
	};

	var baseView = Backbone.View.extend({
		initialize: function(options) {
			this._initialize(options);
			var _this = this;
			(function() {
				if (_this.$el.width() > 0) {

					var $this,$tooltips,$popovers,options,id,type;

					/* popover */
					$popovers = _this.$('[data-toggle-popover]');
					$popovers.hover(function(){
						$this = $(this);
						options   = $this.attr('data-toggle-popover').split(',');
						id        = options[0] || '';
						type      = options[1] || 'default';
						placement = options[2] || 'right';
						$this.attr('data-content', contents[id]);
						$this.attr('title', titles[type]);
						$this.attr('data-placement', placement);
						$this.attr('data-trigger', 'focus');
						// $this.addClass(type+'-popover');
						$this.attr('data-animation','false');
						$this.attr('data-html','true');
						$this.popover('show');
					}, function(){
						$this.popover('hide');
					});

					// /* tooltip */
					// $tooltips  = _this.$('[data-toggle-tooltip]');
					// $tooltips.hover(function(){
					// 	$this = $(this);
					// 	options   = $this.attr('data-toggle-tooltip').split(',');
					// 	id        = options[0] || '';
					// 	type      = options[1] || '';
					// 	placement = options[2] || 'top';
					// 	$this.addClass(type+'-tooltip');
					// 	$this.attr('data-placement', placement);
					// 	$this.attr('data-animation','false');
					// 	$this.tooltip('show');
					// }, function(){
					// 	$this.tooltip('hide');
					// })
				} else {
					setTimeout(arguments.callee, 500);
				}
			})();
		    return this;
		},

	});
	return baseView;
});
