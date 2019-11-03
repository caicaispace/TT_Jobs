define(['backbone', 'mu/cache', 'mu/common'],
    function (Backbone, cache, common) {
        var view = Backbone.View.extend({

            tagName: 'div',
            className: 'container-fluid',

            initialize: function (options) {
                this.$insertDOM = options.$insertDOM;
                _.bindAll(this, 'init');
                cache.gets([
                    {src: APP_PATH + '/template/resources/server.html'},
                    // {src: APP_NAME + '/monitor/system', type: 'json'},
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

            init: function (results) {
                this._res = results;
                this._tpl = results[0].split('||&&||');
                this.render();
            },

            render: function () {
                this.tpl();
                this.$el.html(_.template(this._tpl.init)());
                this.$insertDOM.html(this.el);
                return this;
            }
        });
        return view;
    }
);
