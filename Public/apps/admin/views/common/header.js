define(['backbone', 'mu/cache', 'mu/common'],
	function (Backbone, cache, common) {
		var view = Backbone.View.extend({

			tagName: 'ul',
			className: 'nav navbar-top-links navbar-right',

			events: {
				'click #db-manage': 'dbManage',
				'click #clock-log': 'clockLog',
				'click #config': 'config',
				'click #admin': 'admin',
				'click #logout': 'logout',
			},

			initialize: function (options) {
				_.bindAll(this, 'init');
				cache.gets([
					{ src: APP.APP_PATH + '/template/common/header.html' }
				], this.init);
			},

			init: function (results) {
				this.res = results;
				this.tpl = results[0].split('||&&||');
				this.render();
			},

			render: function () {
				// this.$el.html(this.tpl[0]);
				// this.$el.appendTo('#header');
			},

			/* 数据备份 */
			dbManage: function () {
				var func = _.bind(function (DB) {
					new DB();
				}, this);
				require(['v/db_manage'], func);
			},

			/* 打卡记录 */
			clockLog: function () {
				var func = _.bind(function (DB) {
					new DB();
				}, this);
				require(['v/clock_log'], func);
			},

			/* 系统配置 */
			config: function () {
				var func = _.bind(function (CA) {
					new CA();
				}, this);
				require(['v/config_attendance'], func);
			},

			/* 后台管理 */
			admin: function () {
				var func = _.bind(function (A) {
					new A();
				}, this);
				require(['v/admin'], func);
			},

			logout: function (e) {
				var _this = e.currentTarget;
				var success = function (data) {
					if (data.status === 1)
						window.location.reload(true);
				}
				$.post(_this.href, {}, success, 'json');
				return false;
			}

		});
		return view;
	});
