define(['backbone', 'mu/cache', 'mu/common'], function(Backbone, cache, common) {
	var view = Backbone.View.extend({

		tagName: 'div',
		className: 'container-fluid',

		events:{
			'click #test': 'singleton',
			'click #test-sideDialog': 'sideDialog',
			'click #test-notifications': 'notifications',
		},

		initialize: function(options) {
			this.$insertDOM = options.$insertDOM;
			_.bindAll(this, 'init');
			cache.gets([{
				src: APP.APP_PATH + '/template/test.html'
			}, ], this.init);
		},

		init: function(results) {
			this.res = results;
			this.render();
		},

		render: function() {
			this.$el.html(this.res[0]);
			// this.$el.html(this.tpl);
			this.$insertDOM.html(this.el);
			// var func = _.bind(this.MorrisDatas, this);
			// require(['morrisjs', 'raphael'], func);
			// var test = this.singleton();
			// console.log(test);
			return this;
		},

		sideDialog: function(){
			require(['mu/sideDialog'], function(SideDialog){
				new SideDialog({
				    title: '详情 [ssss]',
				    position: 'right',
				    html: this.el,
				    buttons_type: 1,
				    width: 1000,
				    show_footer: 2
				});
			})
		},

		notifications: function(){
			require(['notification'], function(notification){
    			notification({type:'error', msg: 'notification'});
    			notification({type:'success', msg: 'notification'});
			})
		},

		singleton: function(){
			var singleton = (function(i){
				this.name = 'name';
				console.log(this.name);
				// var i = 0;
				// if(name ==  ''){
				// 	name = 'yes';
				// 	console.log('0ssssss');
				// }
				// return function(){
				// 	if(i){
				// 		console.log('1');
				// 		return i;
				// 	}else{
				// 		i++
				// 		console.log(i);
				// 		return i;
				// 	}
				// }();
				i++
				console.log(i);
			})(1);
			console.log(window.name);
			return singleton;
		},

		tpl: function(){
			var html = '<button id="test">button</button>';
			return html;
		}

	});
	return view;
});
