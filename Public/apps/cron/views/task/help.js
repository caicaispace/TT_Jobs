define(['backbone', 'mu/cache', 'mu/common', 'com/table', 'com/dialog'],
    function (Backbone, cache, common, Table, Dialog) {
        var view = Backbone.View.extend({

            initialize: function (options) {
                this._opt = options;
                this.$insertDOM = options.$insertDOM;
                _.bindAll(this, 'init');
                cache.gets([
                    {src: APP_PATH + '/template/task/help.html'},
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
                this.$el.html(this._tpl.init);
                this._Dialog = new Dialog({
                    title: '帮助文档',
                    type: 'close',
                    size: 'wide',
                    model: this._model,
                    html: this.el,
                    onHidden: _.bind(this._remove, this)
                });
            },

            _remove: function () {
                this._Dialog.hide();
                this.remove().off();
            }
        });
        return view;
    }
);