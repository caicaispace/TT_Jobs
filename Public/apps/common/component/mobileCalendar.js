define(['backbone', 'mu/cache', 'moment'], function(Backbone, cache, moment) {
	var view = Backbone.View.extend({
		tagName: 'div',
		className: 'calendar',
		events: {
			'click [data-opt = month_click]': 'monthClick',
			'click [data-opt = day_click]': 'dayClick'
		},

		_get: {
			full_date: new Date().format('yyyy-MM-dd'),
			date: new Date().format('yyyy-MM'),
			day: new Date().format('d'),
			url: '/api/clock/clockLog'
		},

		initialize: function(options) {
			this.options = options;
			this._get.url = options.url;
			_.bindAll(this, 'init');
			cache.gets([
				{src: '/apps/common/template/mobileCalendar.html'},
				{src: this._get.url, type:'ec'}
			], this.init);
			// console.log(this._get.url);
		},

		init: function(results) {
			this._results = results;
			this.collection = results[1];
			this.collection.on('reset', this.render, this);
			this.render();
		},

		render: function() {

			var
				date = this._get.date,
				models = this.collection.models;

			var
				month_days = this.getDateNum('md', date),
				last_month = this.getDateNum('lm', date),
				next_month = this.getDateNum('nm', date),
				week_month_day1 = this.getDateNum('wmd', date),
				week_month_day30 = moment(date + '-' + month_days).format('e'),
				last_month_days = this.getDateNum('md', last_month + '-01') - week_month_day1;

			var datas = {
				date: date,
				models: models,
				last_month: last_month,
				next_month: next_month,
				month_days: month_days,
				last_month_days: last_month_days,
				week_month_day1: week_month_day1,
				week_month_day30: week_month_day30
			}

			this.$el.html(_.template(this._results[0])({
				datas: datas,
				moment: moment
			}));

			// 默认点击今天
			this.$('[data-date="'+this._get.full_date+'"]').click();
			// return this;
		},

		/**
		 * 月份切换
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		monthClick: function(e) {
			var date = $(e.currentTarget).attr('data-date');
			this._get.date = date;
			this.collection.url = this._get.url + '?date=' + date;
			this.collection.fetch({
				reset: true
			});
		},

		/**
		 * 点击某一天
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		dayClick: function(e) {
			var $dom = $(e.currentTarget);
			$dom.css({
				background: '#FFF3C4'
			}).siblings().css({
				background: ''
			});
			var date = $dom.attr('data-date');
			// var month = $dom.attr('data-month');
			// var day   = $dom.attr('data-day');
			var index = $dom.attr('data-index');
			var data = {
				date: date,
				model: this.collection.models[index]
			}
			this.trigger('day_click', data);
		},

		statusClick: function(e) {

		},

		/**
		 * 获取日期
		 * @param  {string} type 日期类型
		 * @param  {string} date 2015-06
		 * @return {[type]}      [description]
		 */
		getDateNum: function(type, date) {

			var date = date ? date : moment().format('YYYY-MM-DD');
			var arr = date.split('-');
			var year = arr[0];
			var month = arr[1];
			var day = arr[3];
			var num;

			switch (type) {
				case 'wmd': //week day is this date of the month first day (对应月份的第一天星期几)
					num = moment(year + '-' + month + '-01').format('e');
					break;
				case 'md': // how many days this date of month （对应月份有多少天）
					num = moment(date).endOf('month').format('DD');
					break;
				case 'lm': // this date of the last month （上个月）
					num = moment(date).add(-1, 'month').format('YYYY-MM');
					break;
				case 'nm': // this date of the next month （下一月）
					num = moment(date).add(1, 'month').format('YYYY-MM');
					break;
				default: // now date
					num = moment().format('YYYY-MM');
			}
			return num;
		}
	});
	return view;
});