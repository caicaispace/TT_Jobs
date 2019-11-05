define(['backbone', 'mu/cache', 'enquire', 'store'],
    function (Backbone, cache, enquire, store) {
        var view = Backbone.View.extend({
            tagName: 'div',
            className: 'sidebar-nav navbar-collapse',
            events: {
                'click .dropdown': 'dropdown',
                'click #sidebar-toggle-btn': 'toggle'
            },

            _doms: {},

            initialize: function (options) {
                _.bindAll(this, 'init');
                cache.gets([
                    {src: APP.APP_PATH + '/template/common/sidebar.html'}
                    // {src:'/home/frame/left',type:'json'}
                ], this.init);
            },

            init: function (results) {
                this.res = results;
                this.render();
            },

            render: function () {
                this.$el.html(this.res[0]);
                this.$el.appendTo('#sidebar');
                this._cache();
                if (store.get('sidebar') === 'hide')
                    this.toggle('hide');
                this.enquirejs();
                return this;
            },

            _cache: function () {
                this._doms.sidebar = $('#sidebar');
                this._doms.sidebar_toggle_btn_i = $('#sidebar #sidebar-toggle-btn i');
                this._doms.page_wrapper = $('#page-wrapper');
            },

            dropdown: function (e) {
                var $this = $(e.currentTarget);
                var $siblings = $this.parent('li').siblings('li[class=dropdown-open]');
                $this.nextAll().slideToggle(100);
                $this.parent('li').toggleClass('dropdown-open');
                $siblings.children('.dropdown').nextAll().slideUp(100);
                $siblings.removeClass();
                if (store.get('sidebar') === 'hide')
                    this.toggle(false);
            },

            toggle: function (status) {
                var $page_wrapper = this._doms.page_wrapper;
                var $sidebar = this._doms.sidebar;
                var $menus = $sidebar.find('ul li');
                var $menus_title = $menus.has('a i').find('a span');
                $menus.children('a').nextAll().hide();
                if (store.get('sidebar') === 'show' || status === 'hide') {
                    $page_wrapper.css({'margin-left': 50});
                    $sidebar.width(50);
                    $menus_title.hide();
                    store.set('sidebar', 'hide');
                    this._doms.sidebar_toggle_btn_i.attr('class', 'fa fa-chevron-right')
                } else {
                    $page_wrapper.css({'margin-left': ''});
                    $sidebar.width('');
                    $menus_title.show();
                    store.set('sidebar', 'show');
                    this._doms.sidebar_toggle_btn_i.attr('class', 'fa fa-chevron-left')
                }
                ;
            },

            // 媒体查询
            enquirejs: function () {
                enquire.register("screen and (max-width:768px)", {
                    match: function () {
                        $('div.navbar-collapse').addClass('collapse');
                    },
                });
            }
        });
        return view;
    }
);
