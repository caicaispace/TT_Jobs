define(['backbone', 'mu/cache', 'mu/common'],
    function (Backbone, cache, common) {
        var view = Backbone.View.extend({

            events: {
                'click #db-manage': 'dbManage'
                , 'click #clock-log': 'clockLog'
                , 'click #config': 'config'
                , 'click #admin': 'admin'
                , 'click #logout': 'logout'
                , 'click #task-help': 'taskHelp'
            },

            initialize: function (options) {
                this.opt = options;
                this.$insertDOM = options.$insertDOM;
                _.bindAll(this, 'init');
                cache.gets([
                    {src: APP.APP_PATH + '/template/common/header.html'}
                ], this.init);
            },

            init: function (results) {
                this._res = results;
                this._tpl = results[0].split('||&&||');
                this.render();
            },

            tpl: function () {
                var tpls = this._tpl;
                var tpl = {}, arr = [], k, v;
                for (var i = 0; i < tpls.length; i++) {
                    arr = $.trim(tpls[i]).split('|&|');
                    k = arr[0];
                    v = arr[1];
                    tpl[k] = v;
                }
                this._tpl = tpl;
            },

            render: function () {
                this.tpl();
                var html = _.template(this._tpl.init)({session: window.APP.session});
                this.$el.html(html);
                this.$insertDOM.html(this.el);
                require(['jquery_fullscreen'], function () {
                    this.$('#view-fullscreen').fullscreen();
                });
                return this;
            },

            taskHelp: function () {
                require(['v/task/help'], _.bind(function (View) {
                    new View({view: this});
                }, this));
                return false;
            },

            logout: function (e) {
                var _this = e.currentTarget;
                var success = function (data) {
                    if (data.status === 1)
                        window.location.reload(true);
                };
                $.post(_this.href, {}, success, 'json');
                return false;
            }

        });
        return view;
    }
);
