;(function (window, undefined) {
    window.Functions = window.__f = {

        /***********************************  数组 Array ***********************************/

        // recursionFor: function(data, mark){
        //     var mark = mark || 'child';

        // },

        /**
         * 数组长度
         * @param array
         * @returns {number}
         */
        arrLen: function (array) {
            var i, count = 0;
            for (i in array)
                count += 1
            return count
        },

        /**
         * 数组删除
         * @param array
         * @param index
         * @returns {*}
         */
        arrDelete: function (array, index) {
            if (this._isArray(array) === false)
                return;
            index = parseInt(index);
            if (index < 0) {
                return array;
            } else {
                return array.slice(0, index).concat(array.slice(index + 1, array.length))
            }
        },

        /**
         * 数组插入
         * @param array
         * @param value
         * @param index
         * @returns {*}
         */
        arrInsert: function (array, value, index) {
            index = parseInt(index);
            array.splice(index, 0, value);
            return array;
        },

        /**
         * 二维数组去重
         * @param arr1
         * @param arr2
         * @param pk
         * @returns {*}
         */
        arrDiff: function (arr1, arr2, pk) {
            if (arr1 === void 0 || arr1.length <= 0 || arr2 === void 0 || arr2.length <= 0) {
                return arr2;
            }
            var
                i,
                temp,
                arr3 = [],
                list = arr1.length >= arr2.length ? arr1 : arr2,
                list2 = list === arr1 ? arr2 : arr1;
            pk = pk || 'id';
            for (i in list) {
                temp = list2[i] === void 0 ? temp : list2[i];
                if (list[i][pk] !== temp[pk])
                    arr3[i] = list[i];
            }
            return arr3;
        },

        /**
         * 数组转对象
         * @param  {Array}  array
         * @param  {int}  start       其实索引
         * @param  {Boolean} is_like_arr 是否转类数组对象
         * @return {Array}
         */
        arrToObj: function (array, start, is_like_arr) {
            var obj = {},
                len = array.length,
                i = 0;
            start = (start !== void 0 && start > 0) ? start : 0;
            len = array.length;
            for (i; i < len; i++) {
                obj[i + start] = array[i];
            }
            if (is_like_arr !== void 0 && is_like_arr) {
                obj['length'] = len;
            }
            return obj;
        },

        /***********************************  对象 Object ***********************************/

        /**
         * 类数组对象转数组
         * @param {Object}  obj
         * @returns {T[]}
         */
        likeArrObjToArr: function (obj) {
            var len = 1, i;
            if (obj['length'] === void 0 || obj['length'] === '') {
                for (i in obj) {
                    len++;
                }
                obj['length'] = len;
            }
            return Array.prototype.slice.apply(obj);
        },

        /**
         * 对象转数组
         * @param  {Object} obj
         * @return {Array}
         */
        objToArr: function (obj) {
            var arr = [], i;
            for (i in obj)
                arr.push(obj[i]);
            return arr;
        },

        /***********************************  字符串 String ***********************************/

        /**
         * js截取字符串，中英文都能用
         * @param str：需要截取的字符串
         * @param len: 需要截取的长度
         */
        cutstr: function (str, len) {
            var str_length = 0;
            var str_len = 0;
            str_cut = new String();
            str_len = str.length;
            for (var i = 0; i < str_len; i++) {
                a = str.charAt(i);
                str_length++;
                if (escape(a).length > 4) {
                    //中文字符的长度经编码之后大于4
                    str_length++;
                }
                str_cut = str_cut.concat(a);
                if (str_length >= len) {
                    str_cut = str_cut.concat("...");
                    return str_cut;
                }
            }
            //如果给定字符串小于指定长度，则返回源字符串；
            if (str_length < len) {
                return str
            }
        },

        /***********************************  其他工具 Tools ***********************************/

        /**
         * 函数节流
         * @param  {Function}   fn           function
         * @param  {int}        delay        连续调用时间间隔
         * @param  {int}        mustRunDelay
         */
        throttle: function (fn, delay, mustRunDelay) {
            var timer = null;
            var t_start;
            return function () {
                var context = this, args = arguments, t_curr = +new Date();
                clearTimeout(timer);
                if (!t_start) {
                    t_start = t_curr;
                }
                if (t_curr - t_start >= mustRunDelay) {
                    fn.apply(context, args);
                    t_start = t_curr;
                }
                else {
                    timer = setTimeout(function () {
                        fn.apply(context, args);
                    }, delay);
                }
            };
        },

        /**
         * 元素属否渲染完成
         * @param  {jQuery}     $ele        jquery 对象
         * @param  {Function}   renderDone  渲染完成回调
         * @param  {int}        timeout     超时时间 秒
         * @param  {Function}   loadTimeout 加载超时回调
         * @return
         */
        isEleRenderDone: function ($ele, renderDone, timeout, loadTimeout) {
            var _this = this;
            var num = 20 * (timeout || 300);
            (function () {
                if ($ele.width() > 0 || $ele.is(':visible')) {
                    if (_this.isFunction(renderDone))
                        renderDone();
                    return;
                }
                if (num <= 0) {
                    if (_this.isFunction(loadTimeout))
                        loadTimeout();
                    return
                }
                setTimeout(arguments.callee, 50);
                num--;
            })();
        },

        /**
         *
         * @param {Function}    fun
         * @returns {boolean}
         */
        isFunction: function (fun) {
            return Object.prototype.toString.apply(fun) === '[object Function]'
        },

        /**
         *
         * @param {Object}      obj
         * @returns {boolean}
         */
        isArray: function (obj) {
            return Object.prototype.toString.apply(obj) === '[object Array]'
        },

        /**
         * 打印 JavaScript 函数调用堆栈
         */
        printCallStack: function () {
            var i = 0;
            var fun = arguments.callee;
            do {
                fun = fun.arguments.callee.caller;
                console.log(++i + ': ' + fun);
            } while (fun);
        },

        /**
         * 打印全局函数
         */
        getGlobalVar: function () {
            (function (topWindow, document) {
                var iframeWindow;
                var whiteList = [/*'jQuery', '$', ...*/];
                var ret = [];

                function checkGlobalVar() {
                    var iframe = document.createElement('iframe'), i, originValue;
                    document.body.appendChild(iframe);
                    iframeWindow = iframe.contentWindow;
                    for (i in topWindow) {
                        if (!(i in iframeWindow) && !~whiteList.indexOf(i)) {
                            originValue = topWindow[i];
                            topWindow[i] = '耗子么么哒'; // 写一个不可能是系统预设的值
                            if (topWindow[i] === '耗子么么哒') {
                                iframeWindow.console.info(i); // 防止重写了topWindow的console
                                ret.push(i);
                            }
                            topWindow[i] = originValue;
                        }
                    }
                    iframeWindow.console.warn('共找到' + ret.length + '个全局变量;');
                    document.body.removeChild(iframe); // 干完坏事会死灭迹
                    iframeWindow = null;
                }

                setTimeout(function () {
                    if (!document.body) {
                        alert('页面还没加载完！');
                        return;
                    }
                    checkGlobalVar();
                }, 1000)
            })(top, document);
        },

        /**
         * 时间间隔计算(间隔天数)
         * @param {String} start_date
         * @param {String} end_date
         */
        getDateDiff: function (start_date, end_date) {
            var startTime = new Date(Date.parse(start_date.replace(/-/g, "/"))).getTime();
            var endTime = new Date(Date.parse(end_date.replace(/-/g, "/"))).getTime();
            var dates = Math.abs((startTime - endTime)) / (1000 * 60 * 60 * 24);
            return parseInt(dates);
        },

        /**
         * 倒计时
         * @param  {jQuery}   $dom
         * @param  {int}      now_time 当前时间戳
         * @param  {int}      end_time 结束时间戳
         * @param  {jQuery}    $show
         * @param  {Function} callback
         * @return {[type]}
         */
        countdown: function ($dom, now_time, end_time, $show, callback) {
            var
                html, h, m, s,
                t = end_time - now_time,
                arr = ['00', '小时', '分', '秒'];
            now_time = now_time || parseInt(new Date().getTime() / 1000);
            var si = setInterval(function () {
                t--;
                h = Math.floor(t / 60 / 60 % 24);
                m = Math.floor(t / 60 % 60);
                s = Math.floor(t % 60);
                if (h <= 0 && m <= 0 && s <= 0) {
                    h = m = s = 0;
                    callback(true);
                    clearInterval(si);
                } else {
                    callback(false);
                }
                if ($show !== void 0) {
                    html = (h || arr[0]) + arr[1] + (m || arr[0]) + arr[2] + (s || arr[0]) + arr[3];
                    $show.html(html);
                }
            }, 1000);
        },

        /**
         * 检测图片是否可用
         * @param  {String}   src      图片路径
         * @param  {Function} callback
         */
        imageExists: function (src, callback) {
            var img = new Image();
            img.onload = function () {
                callback(true);
            };
            img.onerror = function () {
                callback(false);
            };
            img.src = src;
        },

        /**
         * 二维数组去重
         * @param  {Array} arr1
         * @param  {Array} arr2
         * @param  {String} pk   去重标识 默认为 id
         * @return {Array}
         */
        diff: function (arr1, arr2, pk) {
            if (arr1 === void 0 || arr1.length <= 0 || arr2 === void 0 || arr2.length <= 0)
                return arr2;
            var
                i,
                temp,
                arr3 = [],
                pk = pk || 'id';
            list = arr1.length >= arr2.length ? arr1 : arr2,
                list2 = list === arr1 ? arr2 : arr1;
            for (i in list) {
                temp = list2[i] === void 0 ? temp : list2[i];
                if (list[i][pk] !== temp[pk])
                    arr3[i] = list[i];
            }
            return arr3;
        },

        /**
         * 获取对象中指定值
         * @param  {Array} arr
         * @param  {String} pk
         * @return {Array}
         */
        arrObjVal: function (arr, pk) {
            var i, values = [];
            for (i in arr) {
                values.push(arr[i][pk]);
            }
            return values;
        },

        /**
         * 评星
         * @param  {int} num   星星数
         * @param  {int} level 星星级别
         * @return {String}
         */
        star: function (num, level) {
            num = num || 1;
            level = level || 10;
            var arr = [];
            for (var i = 0; i < level; i++) {
                arr.unshift('★');
                arr.push('☆');
            }
            return arr.join('').substring(level - num, level * 2 - num);
        },

        /**
         * 生成表格
         * @param  {Number} tr tr
         * @param  {Number} td ts
         * @param  {String} content
         * @return {String}
         */
        tables: function (tr, td, content) {
            var a = arguments;
            tr = (a[0] || 2) + 1;
            td = (a[1] || 2) + 1;
            content = (a[2] || 'content');
            return [
                '<table>', new Array(tr).join(
                    [
                        '<tr>',
                        new Array(td).join('<td>' + content + '</td>'),
                        '</tr>'
                    ].join('')),
                '</table>'
            ].join('');
        },

        /**
         * 生成唯一值
         * 可设置前缀
         * 可设置初始值
         * @returns {{set_prefix: set_prefix, set_seq: set_seq, gensym: gensym}}
         */
        serialMaker: function () {
            var prefix = '';
            var seq = 0;
            return {
                set_prefix: function (p) {
                    prefix = String(p);
                },
                set_seq: function (s) {
                    seq = s;
                },
                gensym: function () {
                    var result = prefix !== '' ? prefix + seq : seq;
                    seq += 1;
                    return result;
                }
            };
        },

        /**
         * 生成唯一值
         * 可设置初始值
         * @returns {{set_seq: set_seq, gensym: gensym}}
         */
        serialMakerNum: function () {
            var seq = 0;
            return {
                set_seq: function (s) {
                    seq = s;
                },
                gensym: function () {
                    seq += 1;
                    return seq;
                }
            };
        },

        /**
         * GUIDx4
         * @param len
         * @returns {string}
         * @constructor
         */
        GUIDx4: function (len) {

            var guid = '',
                i;

            len = len || 1;
            for (i = 0; i < len; i += 1)
                guid += (((1 + Math.random()) * 0x10000) | 0)
                    .toString(16)
                    .substring(1);

            return guid;
        },

        /**
         * GUID 固定长度
         * @returns {string}
         * @constructor
         */
        GUID: function () {
            var i, n, guid = "";
            for (i = 1; i <= 32; i++) {
                n = Math.floor(Math.random() * 16.0).toString(16);
                guid += n;
                if ((i === 8) || (i === 12) || (i === 16) || (i === 20))
                    guid += "-";
            }
            return guid;
        }

    };

    /************************************** 原型函数 **********************************************/

    // Array.prototype.baoremove = function(dx) {
    //     if(isNaN(dx)||dx>this.length){return false;}
    //     this.splice(dx,1);
    // }

    if (!String.strRepeat) {
        /**
         * 重复一个字符串 原型函数
         * @param  {int} len
         * @return {string}
         */
        String.prototype.strRepeat = function (len) {
            return new Array(len + 1).join(this);
        };
    }

    if (!String.strReplaceAll) {
        /**
         * 替换全部字符串
         * @param  {string}  substr
         * @param  {string}  replacement
         * @return {string}
         */
        String.prototype.strReplaceAll = function (substr, replacement) {
            return this.replace(new RegExp(substr, "gm"), replacement);
        }
    }

    /************************************** 日期函数 **********************************************/

    /**
     * 日期格式化
     * @param  {string} format yyyy-MM-dd hh:mm:ss w
     * @return {string}        2016-04-22 15:01:00 星期五
     */
    Date.prototype.format = function (format) {
        var weeks = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
        var o = {
            "M+": this.getMonth() + 1, //month
            "d+": this.getDate(), //day
            "h+": this.getHours(), //hour
            "m+": this.getMinutes(), //minute
            "s+": this.getSeconds(), //second
            "q+": Math.floor((this.getMonth() + 3) / 3), //quarter
            "S": this.getMilliseconds(), //millisecond
            "w": weeks[this.getDay()], //week
        };
        if (/(y+)/.test(format)) {
            format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
            }
        }
        return format;
    };

    /**
     * 日期格式化
     * @param  {string} date_string yyyy-MM-dd hh:mm:ss w
     * @return {int}                2016-04-22 15:01:00 星期五
     */
    Date.prototype.toTimestamp = function (date_string) {
        var timestamp = Date.parse(new Date(date_string));
        return timestamp / 1000;
    };

    return window;
})(window);
