define(['backbone', 'mu/cache', 'enquire', 'store'],
function (Backbone, cache, enquire, store) {
    var view = Backbone.View.extend({

        id: 'side-menu',
        tagName: 'ul',
        className: 'nav',

        events: {
            'click .dropdown': 'dropdown',
            'click #sidebar-toggle-btn': 'toggle'
        },

        _doms: {},

        initialize: function (options) {
            this.opt = options;
            this.$insertDOM = options.layout.$left;
            _.bindAll(this, 'init');
            cache.gets([
                { src: APP.APP_PATH + '/template/common/left_menu.html' }
                // {src:'/home/frame/left',type:'json'}
            ], this.init);
        },

        init: function (results) {
            this.res = results;
            this.render();
        },

        render: function () {
            this.$el.html(this.res[0]);
            this.$insertDOM.html(this.el);
            this.useMetisMenu()
            this._cache();
            if (store.get('left') == 'hide')
                this.toggle('hide');
            return this;
        },

        _cache: function () {
            this._doms.left = $('.sidebar');
            this._doms.toggle_btn = $('#sidebar-toggle-btn i');
            this._doms.content = $('#page-wrapper');
            this._doms.menus_title = this.$el.find('li').has('a i').find('a span');
        },

        toggle: function (status) {
            var $content = this._doms.content;
            var $left = this._doms.left;
            var $menus_title = this._doms.menus_title;
            if (typeof store.get('left') == 'undefined' || store.get('left') == 'show' || status == 'hide') {
                $content.css({ 'margin-left': 50 });
                $left.width(50);
                $menus_title.hide();
                store.set('left', 'hide');
                this._doms.toggle_btn.attr('class', 'fa fa-chevron-right')
            } else {
                $content.css({ 'margin-left': '' });
                $left.width('');
                $menus_title.show();
                store.set('left', 'show');
                this._doms.toggle_btn.attr('class', 'fa fa-chevron-left')
            };
        },

        useMetisMenu: function () {
            require(['metisMenu'], function () {
                require(['sb_admin_2']);
            });
        }

    });
    return view;
});