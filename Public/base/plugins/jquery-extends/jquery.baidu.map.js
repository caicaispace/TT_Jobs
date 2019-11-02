/*
 * @Author: 0ivi0
 * @Date:   2016-05-30 19:43:01
 * @Last Modified by:   0ivi0
 * @Last Modified time: 2017-05-23 19:17:52
 */
/*
    AMD 加载必须使用 requirejs 的 async 插件
 */
;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery', '/base/plugins/require/BMap.js'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS. Register as a module
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($, BMap) {

    var _bind, _bindAll, _init, _view, _delegateEvents;

    $.fn.bmap = function (opt) {

        if (this[0] === void 0) throw new Error('-- Your selector dom has not find :( --');
        if (!opt) opt = {};

        // 默认配置
        opt = $.extend(true, {
            area: '北京',
            zoom: 15,
            width: '',
            height: '400px',
            padding: '0px',
            // point: '118.32457,32.317351',
            callback: '',
            getClickPoint: ''
        }, opt);

        this[0].style.width = opt.width;
        this[0].style.height = opt.height;
        this[0].style.padding = opt.padding;
        opt.$el = this;
        return new _bind(_init, _view())(opt);
    };

    var _getMapCenter = function (obj) {

        var fun = _bind(function (e) {
            var point = this.map.getCenter();
            point = point.lng + "," + point.lat;
            this.onLoad = true;
            this.point = point;
            if (typeof this.opt.callback === 'function') {
                this.opt.callback(point);
            }
        }, obj);

        obj.map.addEventListener('load', fun);
    };

    _view = function () {

        return {

            point: '',
            onLoad: false,

            initialize: function (options) {
                this.opt = options;
                this.render();
            },

            render: function (date) {
                this.setArea();
                return this;
            },

            /**
             * 设置宽高
             * @param {string} width
             * @param {string} height
             */
            setWH: function (width, height) {
                this.$el[0].style.width = width;
                this.$el[0].style.height = height;
                return this;
            },

            /**
             * 设置地点
             * @param {string} area 地区中文名
             * @param {int}    zoom 缩放
             */
            setArea: function (area, zoom) {
                var id = this.$el[0].id;
                this.map = new BMap.Map(id);
                // this.map.addControl(new BMap.MapTypeControl());
                this.map.enableScrollWheelZoom(true);
                area = area || this.opt.area;
                zoom = zoom || this.opt.zoom;
                this.map.centerAndZoom(area, zoom);
                this.onLoad = false;
                _getMapCenter(this);
                this._seetAddClickEvent();
                return this;
            },

            /**
             * 设置坐标
             * @param {string} point  '118.32457,32.317351'
             * @param {int}    zoom    缩放
             */
            setPoint: function (point, zoom) {
                var id = this.$el[0].id;
                this.map = new BMap.Map(id);
                point = point || this.opt.point
                zoom = zoom || this.opt.zoom;
                this.point = point;
                var arr = point.split(',');
                var point = new BMap.Point(arr[0], arr[1]);
                this.map.centerAndZoom(point, zoom);
                this.onLoad = false;
                _getMapCenter(this);
                this._seetAddClickEvent();
                return this;
            },

            /**
             * 设置点击事件
             */
            _seetAddClickEvent: function () {
                var fn = _bind(function(e){
                    if (typeof this.opt.getClickPoint === 'function') {
                        this.opt.getClickPoint(e.point.lng + "," + e.point.lat);
                    }
                }, this)
                this.map.addEventListener("click", fn);
            },

            /**
             * 坐标实时显示
             * @return {object} this
             */
            showPoint: function () {
                var e, posx, posy, $show_point, ele;
                this.$el.append('<span id="jquery-bmp-show-point"></span>');
                $show_point = this.$el.find('#jquery-bmp-show-point');
                ele = $show_point[0];

                ele.style.display = 'block';
                ele.style.height = '20px';
                ele.style.background = '#ff0000';
                ele.style.position = 'absolute';
                ele.style.color = '#fff';
                ele.style.padding = '2px';
                ele.style.overflow = 'hidden';

                this.map.addEventListener("mouseover", function (e) {
                    ele.style.display = 'block';
                });

                this.map.addEventListener("mousemove", function (e) {
                    e = e ? e : window.event;
                    // posx = e.clientX - 260;
                    // posy = e.clientY - 240;
                    posx = e.clientX - 880;
                    posy = e.clientY - 120;
                    ele.style.left = posx + "px";
                    ele.style.top = posy + "px";
                    $show_point.html(e.point.lng + "," + e.point.lat);
                });

                this.map.addEventListener("mouseout", function (e) {
                    ele.style.display = 'none';
                });

                return this;
            },

            /**
             * 设置静态 Marker 不支持链式调用
             * @param {string} point '118.32457,32.317351'
             */
            setMarker: function (point) {
                var _this = this;
                var si = setInterval(function () {
                    if (_this.onLoad === true) {
                        clearInterval(si);
                        _this.map.removeOverlay(_this.marker);
                        point = point || _this.point;
                        var p = point.split(',')
                        var poi = new BMap.Point(p[0], p[1]);
                        _this.marker = new BMap.Marker(poi);
                        _this.marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
                        _this.map.addOverlay(_this.marker);
                    }
                }, 100)
            },

            /**
             * 设置可拖拽 Marker 不支持链式调用
             * @param {string} point '118.32457,32.317351'
             * @return {callback}     '118.32457,32.317351'
             */
            setDraggingMarker: function (point, callback) {
                var _this = this;
                var si = setInterval(function () {
                    if (_this.onLoad === true) {
                        clearInterval(si)
                        _this.map.removeOverlay(_this.marker);
                        point = point || _this.point;
                        var p = point.split(',');
                        var poi = new BMap.Point(p[0], p[1]);
                        _this.marker = new BMap.Marker(poi);
                        _this.marker.enableDragging();
                        _this.marker.addEventListener("dragend", function (e) {
                            if (typeof _this.opt.callback === 'function')
                                _this.opt.callback(e.point.lng + "," + e.point.lat);
                        });
                        _this.map.addOverlay(_this.marker);
                    }
                }, 100)
            }

        }
    };

    // 初始化
    _init = function (options) {
        this.$el = options.$el;
        this.initialize(options);
        return this;
    };

    // 函数绑定
    _bind = function (fn, context) {
        return function () {
            return fn.apply(context, arguments);
        }
    };
}));