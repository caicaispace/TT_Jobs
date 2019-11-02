define(['backbone','mu/cache','moment'],function(Backbone,cache,moment){
	var view = Backbone.View.extend({
		tagName : 'div',
		className : 'calendar',
		events : {
			'click [data-opt = month_click]' : 'monthClick',
			'click [data-opt = day_click]' : 'dayClick',
			'click [data-opt = status_click]' : 'statusClick',
			'click #custom-current' : 'customCurrent',
		 },

		initialize : function(options){
			this.options = options;
			_.bindAll(this,'init');
			cache.gets([
				{src:'/apps/common/template/calendar.html'}
			],this.init);
		},

		init : function(results){
			this._results = results;
			this._data = this.options.data;
			this.render();
		},

		render : function(date){
			
			var start = moment(parseInt(this.options.start)*1000).format('YYYYMMDD');
			var end   = moment(parseInt(this.options.end)*1000).format('YYYYMMDD');
			var week  = this.options.week;
			var data  = this._data;
			var date             = date ? date : moment().format('YYYY-MM');
			var month_days       = this.getDateNum('md',date);
			var last_month       = this.getDateNum('lm',date);
			var next_month       = this.getDateNum('nm',date);
			var week_month_day1  = this.getDateNum('wmd',date);
			var week_month_day30 = moment(date+'-'+month_days).format('e');
			var last_month_days  = this.getDateNum('md',last_month + '-01') - week_month_day1;

			var datas = {
				date : date,
				week : week,
				data : data,
				timeStart : start,
				timeEnd : end,
				last_month : last_month,
				next_month : next_month,
				month_days : month_days,
				last_month_days : last_month_days,
				week_month_day1 : week_month_day1,
				week_month_day30 : week_month_day30
			};
			this.$el.html(_.template(this._results[0]) ({info:datas,moment:moment}));
		},

		/**
		 * 月份切换
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		monthClick : function(e){
			var date = $(e.currentTarget).attr('data-date');
			this.render(date);
		},

		/**
		 * 点击哪一天
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		dayClick : function(e){
			var $dom = $(e.currentTarget);
			$dom.css({background:'#FFF3C4'}).siblings().css({background:''});
			var date  = $dom.attr('data-date');
			var month = $dom.attr('data-month');
			var day   = $dom.attr('data-day');
			this.trigger('day_click',date);
		},

		statusClick : function(e){

		},

		/**
		 * 获取日期
		 * @param  {string} type 日期类型
		 * @param  {string} date 2015-06
		 * @return {[type]}      [description]
		 */
		getDateNum : function(type,date){

			var date   = date ? date : moment().format('YYYY-MM-DD');
			var arr    = date.split('-');
			var year   = arr[0];
			var month  = arr[1];
			var day    = arr[3];
			var num;

			switch (type){
				case 'wmd': //week day is this date of the month first day (对应月份的第一天星期几)
					num = moment(year+'-'+month+'-01').format('e');
					break;
				case 'md': // how many days this date of month （对应月份有多少天）
					num = moment(date).endOf('month').format('DD');
					break;
				case 'lm': // this date of the last month （上个月）
					num = moment(date).add(-1,'month').format('YYYY-MM');
					break;
				case 'nm': // this date of the next month （下一月）
					num = moment(date).add(1,'month').format('YYYY-MM');
					break;
				default: // now date
					num = moment().format('YYYY-MM');
			}
			return num;
		}
	});
	return view;
});