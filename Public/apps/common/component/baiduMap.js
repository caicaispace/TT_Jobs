/*
* 百度地图
*/
define(['backbone', 'mu/cache', 'com/dialog', 'jquery_bmap'],
function (Backbone, cache, Dialog, jqueryBmap) {
    var view = Backbone.View.extend({

        events: {
            'click [data-id=search]': 'search',
        },

        initialize: function (options) {
            this._opt = options;
			this._getPoint = options.getPoint || ''; //
            _.bindAll(this, 'init');
            cache.gets([
                { src:  '/apps/common/template/baiduMap.html' },
            ], this.init);
        },

        init: function (results) {
            this._res = results;
            this._tpl = results[0];

            this.render();
        },

        render: function() {

            this.$el.html(this._tpl);

            this._$ele = this.$('#component-baidu-map');
            this._$search = this.$('input[name=search]');

            this._Dialog = new Dialog({
                title: '选取坐标',
                position: 'right',
                max_hight: false,
                width: 800,
                html: this.el,
                onShown: _.bind(this.onShown, this),
                onHidden: _.bind(this._remove, this)
            });
            return this;
        },

        onShown: function() {
            setTimeout(_.bind(function(){
                this._bmap = this._$ele.bmap({
                    area: '上海',
                    height: '430px',
                    zoom: 11,
                    // callback: this._getPoint,
                    getClickPoint: this._getPoint
                });
            }, this), 300)
        },

        search: function() {
            this._bmap.setArea(this._$search.val());
        },

        _remove: function () {
            this.remove().off();
        }

    });
    return view;
});