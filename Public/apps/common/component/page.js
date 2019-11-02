/**
 * 分页组件
 * @param  {[type]} Backbone     [description]
 */
define(['backbone'], function(Backbone) {
	var page = Backbone.View.extend({

		tagName: 'div',
		className: 'paginationHtml panel col-md-12 text-right page',
		events: {
			'click #page li a': 'clickPage',
			'click [data-id=go]': 'go'
		},

		_showPageLimit: 5,

		initialize: function() {
			this.model.on('change', this.render, this);
			this.render();
		},

		render: function() {
			var html = this.pageTpl();
			this.$el.html(html);
			return this;
		},

		pageTpl: function() {

            var showPageLimit = this._showPageLimit;
            var limit = this.model.get('limit');
            var total = this.model.get('total');
            var center = Math.ceil(showPageLimit / 2);
            var showPageTotal = Math.ceil(total / limit);
            var showNowPage = parseInt(this.model.get('page'));

			this.showPageTotal = showPageTotal;

			var start = showNowPage <= center || showPageTotal <= showPageLimit
				? 1
				: showNowPage - (center - 1);

			var end = start + showPageLimit - 1;

			if (end > showPageTotal)
				end = showPageTotal;

			if (showPageTotal >= showPageLimit && end - start < showPageLimit - 1)
				start = end - showPageLimit + 1;

			var nextPage = showNowPage === showPageTotal
				? 'NULL'
				: showNowPage + 1;

			var lastPage = showNowPage === 1
				? 'NULL'
				: showNowPage - 1;

            var p = {
                total: total
                , start: start
                , end: end
                , nextPage: nextPage
                , lastPage: lastPage
                , showNowPage: showNowPage
                , showPageTotal: showPageTotal
                , _bottom_custom: this._bottom_custom
            };

			var tpl = '<% if(end != 0){ %>'
					+'<nav class="page-action pager">'
					  +'<ul class="pagination" id="page">'
					    +'<li class="<%= (lastPage=="NULL") ? "disabled" : "up-page" %>" >'
					      +'<a href="javascript:void(0)" aria-label="Previous" data-page="<%= lastPage %>" >'
					        +'<span aria-hidden="true">&laquo;</span>'
					      +'</a>'
					    +'</li>'
						+'<% for (var i = start;i <= end;i++){ %>'
							+'<% if (i==showNowPage){ %>'
								+'<li class="active" ><a href="javascript:void(0);" data-page="NULL" ><%= i %></a></li>'
							+'<% }else %>'
								+'<li><a href="javascript:void(0);" data-page="<%= i %>" ><%= i %></a></li>'
						+'<% } %>'
					    +'<li class="<%= (nextPage=="NULL") ? "disabled" : "next-page" %>" >'
					      +'<a href="javascript:void(0)" data-page="<%= nextPage %>" aria-label="Next" data-page="<%= nextPage %>" >'
					        +'<span aria-hidden="true">&raquo;</span>'
					      +'</a>'
					    +'</li>'
						+'<li><input data-id="go_page" type="text" class="form-control page-jump"></li>'
						+'<li data-id="go" class="page-go"><span class="btn btn-primary page-jump-click" href="javascript:void(0)"  >跳页</span></li>'
						+'<%= _bottom_custom %>'
					  +'</ul>'
					+'</nav>'
					+'<p class="page-info">当前：第<span class="now-show"> <%= showNowPage %> </span> 页 / 共：<%= showPageTotal %> 页 - <%= total %> 条</p>'
					+'<% } else { %>'
						+'<div class="page-error">无数据</div>'
					+'<% } %>';

			return _.template(tpl)(p);
		},

		clickPage: function(e) {
			var click_page = $(e.currentTarget).attr('data-page');
			if (click_page === 'NULL'){
				return;
			}
			this.trigger('clickPage', parseInt(click_page));
		},

		go: function() {
			var go_page = this.$el.find('[data-id=go_page]').val();
			if (go_page > this.showPageTotal) {
				return false;
			}
			if (go_page === ''){
				return;
			}
			this.trigger('clickPage', parseInt(go_page));
		}
	});
    return page;
});
