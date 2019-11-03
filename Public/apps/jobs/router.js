define(['backbone', 'mu/common', 'nprogress'], function (Backbone, common, NProgress) {
    Backbone.ajax = function (options) {
        NProgress.start();
        var success = options.success;
        var alert_types = 'PUT,POST,DELETE';
        options.success = function (resp) {
            if (!resp.status) {
                if (typeof options.validate === 'undefined')
                    return common.error(null, resp);
                return options.error(resp);
            }
            if (success) success(resp);
            if (alert_types.indexOf(options.type) > -1) common.success();
            NProgress.done();
        };
        return Backbone.$.ajax.apply(Backbone.$, arguments);
    };

    /**************** page layout ****************/
    var layout = {
        '$header': $('#headerbar')
        , '$left': $('.sidebar-nav')
        , '$right': $('#rightpanel')
        , '$content': $('#right-content')
        , '$breadcrumb': $('#breadcrumb')
        , '$route_history_back': $('#route-history-back')
        , '$route_history_go': $('#route-history-go')
    };
    /**************** page layout ****************/

    var AppRouter = Backbone.Router.extend({
        routes: {
            "": "task",

            "index": "index",

            "monitor/:action": 'monitor',
            "table/:action": 'table',
            "task/:action": 'task',
            "resources/:action": 'resources',
            "user/:action": 'user',
            "auth/:action": 'auth',

            "test": 'test',

            "*actions": 'defaultRoute'
        },

        index: function () {
            this.show('statistics/dashboard');
        },

        monitor: function (action) {
            var controller = 'monitor';
            action = action || 'system';
            this.show(controller + '/' + action);
        },

        table: function (action) {
            var controller = 'table';
            action = action || 'list';
            this.show(controller + '/' + action);
        },

        task: function (action) {
            var controller = 'task';
            action = action || 'list';
            this.show(controller + '/' + action);
        },

        resources: function (action) {
            var controller = 'resources';
            action = action || 'server';
            this.show(controller + '/' + action);
        },

        user: function (action) {
            var controller = 'user';
            action = action || 'profile';
            this.show(controller + '/' + action);
        },

        auth: function (action) {
            var controller = 'auth';
            action = action || 'admin_list';
            this.show(controller + '/' + action);
        },

        test: function () {
            this.show('test');
        },

        initialize: function () {

        },

        //匹配所有url
        defaultRoute: function (actions) {
            if (actions) {
                //console.log(actions);
            }
        },

        // 面包屑导航
        breadcrumb: function () {
            var current_route = Backbone.history.getFragment();
            var current_routes = current_route.split('/');
            current_route = current_routes.join(' / ');
            layout.$breadcrumb.html(current_route)
        },

        /**
         * 显示视图
         * @param  {string} name    视图名
         * @param  {object} options 传入视图配置
         */
        show: function (name, options) {
            // var before = this.beforeShow();
            // before.loading();
            // var after  = this.afterShow();
            this.breadcrumb();

            options === void 0
                ? options = {$insertDOM: layout.$content}
                : options.$insertDOM = layout.$content;
            // : options = {insertDOM: '#right-content'};

            require(['v/' + name], function (view) {
                APP.currentView = new view(options);
                // APP.currentView.$el.fadeIn(1000);
            });
        },

        /**
         * 前置函数
         * @return {[ object ]}
         */
        beforeShow: function () {
        },

        /**
         * 后置函数
         * @return {[ object ]}
         */
        afterShow: function () {
        }
    });

    var ar = new AppRouter;

    ar.on('route', function (func, arg) {
        if (APP.currentView) APP.currentView.remove().off();
    });

    require(['v/common/header', 'v/common/left_menu'], function (header, leftMenu) {
        new header({
            layout: layout,
            $insertDOM: layout.$header
        });
        new leftMenu({
            layout: layout,
            $insertDOM: layout.$left
        });
        Backbone.history.start();
        layout.$route_history_back.click(function () {
            window.history.back()
        });
        layout.$route_history_go.click(function () {
            window.history.go(1)
        });
        // /* remove route '#' , but you can't refresh on F5 or browser's refresh button */
        // Backbone.history.start({ pushState: true });
        // $(document).on("click", "a:not([data-bypass])", function(evt) {
        // 	var href = { prop: $(this).prop("href"), attr: $(this).attr("href") };
        // 	var root = location.protocol + "//" + location.host + Backbone.history.options.root;

        // 	if (href.prop && href.prop.slice(0, root.length) === root) {
        // 		evt.preventDefault();
        // 		Backbone.history.navigate(href.attr, true);
        // 	}
        // });
    });
});
