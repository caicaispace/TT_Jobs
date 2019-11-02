/**
 *
 */
define(['backbone', 'mu/cache', 'mu/common'], function (Backbone, cache, common) {
    var view = Backbone.View.extend({

        tagName: 'div',
        className: 'container-fluid',

        initialize: function (options) {
            this.$insertDOM = options.$insertDOM;
            _.bindAll(this, 'init');
            cache.gets([
                { src: APP_PATH + '/template/statistics/location.html' },
                // { src: APP_NAME + '/statistics', type: 'json' },
            ], this.init);
        },

        init: function (results) {
            this._res = results;
            this._tpl = results[0].split('||&&||');
            // this._data = results[1]['info'];
            this.render();
        },

        render: function () {
            // var registers = this._data['registers'];
            var html = _.template(this._tpl[1])();
            this.$el.html(html);
            this.$insertDOM.html(this.el);
            this.addMap();
            require(['morrisjs'], _.bind(this._morrisDatas, this));
            return this;
        },

        addMap: function () {
            require(['jquery_bmap'], _.bind(function (jqueryBmap) {
                var $ele = this.$('#component-baidu-map');
                var $search = this.$('input[name=search]');
                var $search_onclick = this.$('[data-id="search"]');
                var bmap = $ele.bmap({
                    area: '上海',
                    height: '382px',
                    zoom: 11,
                    // callback: this._getPoint,
                    getClickPoint: _.bind(this._getPoint, this)
                });
                $search_onclick.on('click', _.bind(function(params) {
                    bmap.setArea($search.val());
                }, this));

            }, this));
        },

        _getPoint: function (point) {
            var $max_km = this.$('input[name=max_km]');
            var max_km = $max_km.val() || 30;
            console.log(point, max_km);
            var data = {
                '__act': 'location',
                'max_km': max_km,
                'point': point
            };
            cache.get(APP_NAME + '/statistics', data, _.bind(function (data) {
                this._morrisDatas(data);
            }, this), 'json');
        },

        _morrisDatas: function (data) {
            if (data['status'] == void 0) {
                return false;
            }
            this.$('#register-area-chart').html('');
            var list = data.info.registers.thirty_day_list;
            var total = data.info.registers.total;
            this.$('#register-total').html(total);
            // 最近 30 天注册趋势
            Morris.Area({
                element: 'register-area-chart',
                data: list,
                xkey: 'date',
                ykeys: ['total'],
                labels: ['总计'],
                pointSize: 2,
                hideHover: 'auto',
                resize: true
            });
        }

    });
    return view;
});
