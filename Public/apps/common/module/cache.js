define(['mu/common'], function (common) {
    return {

        hname: location.hostname ? location.hostname : 'localStatus',
        isLocalStorage: window.localStorage ? true : false,
        dataDom: null,
        prefix: 'S',
        url: '/',

        hashCode: function (str) {
            var h = 0;
            var len = str.length;
            var t = 2147483648;
            for (var i = 0; i < len; i++) {
                h = 31 * h + str.charCodeAt(i);
                if (h > 2147483647) {
                    h %= t; // int溢出则取模
                }
            }
            return this.prefix + h.toString();
        },

        initDom: function () { //初始化userData
            if (!this.dataDom) {
                try {
                    this.dataDom = document.createElement('input'); //这里使用hidden的input元素
                    this.dataDom.type = 'hidden';
                    this.dataDom.style.display = "none";
                    this.dataDom.addBehavior('#default#userData'); //这是userData的语法

                    document.body.appendChild(this.dataDom);
                    var exDate = new Date();
                    exDate.setDate(exDate.getDate() + 30);
                    this.dataDom.expires = exDate.toUTCString(); //设定过期时间
                } catch (ex) {
                    return false;
                }
            }
            return true;
        },

        set: function (key, value, syn) {
            if (APP.debug) {
                return; //调试禁止缓存
            }
            if (typeof (value) !== 'string') {
                value = JSON.stringify(value);
            }
            if (syn) {
                $.post(this.url, {
                    data: value
                }, function () {
                });
            }
            var hashkey = this.hashCode(key);
            if (this.isLocalStorage) {
                window.localStorage.setItem(hashkey, value);
            } else {
                if (this.initDom()) {
                    this.dataDom.load(this.hname);
                    this.dataDom.setAttribute(hashkey, value);
                    this.dataDom.save(this.hname)

                }
            }
        },

        get: function (key, data, callback, dataType, isLocal) {
            var result = this._fastGet(key, dataType);
            if (result == null) {
                $.ajax({
                    url: key,
                    data: data,
                    success: function (result) {
                        if (isLocal)
                            this.set(key, result);
                        callback(result);
                    },
                    dataType: dataType,
                    async: true,
                    context: this
                });
            } else {
                callback(result);
            }
        },

        _fastGet: function (key, dataType) {
            var result, hashkey;
            hashkey = this.hashCode(key);
            if (this.isLocalStorage) {
                result = window.localStorage.getItem(hashkey);
            } else {
                if (this.initDom()) {
                    this.dataDom.load(this.hname);
                    result = this.dataDom.getAttribute(hashkey);
                }
            }
            if (dataType === 'json' && result) {
                return JSON.parse(result);
            }
            return result;
        },

        /**
         * 批量异步获取数据
         * @param  {[type]}   urls     [{src:'',type:null,local:false,path:null}]
         * @param  {Function} callback 回调函数 一个参数，返回数据
         * @return {[type]}            [description]
         */
        gets: function (urls, callback) {
            var len;
            var results = [];
            var mark = 0;
            var i;
            urls = urls || [];
            len = urls.length;
            for (i in urls) {
                if (!urls.hasOwnProperty(i)) continue;
                this._async(i, urls[i], function (i, result) {
                    results[i] = result;
                    mark++;
                    if (mark === len) {
                        callback(results);
                    }
                });
            }
        },

        _async: function (i, url, callback) {
            var path;
            var isRequire = false;

            switch (url.type) {
                case 'ec':
                    isRequire = true;
                    path = 'c/emptyCollection';
                    break;
                case 'em':
                    isRequire = true;
                    path = 'm/emptyModel';
                    break;
                case 'model':
                case 'collection':
                    isRequire = true;
                    options = {};
                    path = url.src;
            }
            if (isRequire) {
                var fn = _.bind(function (obj) {
                    var mc = new obj();
                    if (url.type === 'ec') {
                        mc.url = url.src;
                    }
                    else if (url.type === 'em') {
                        mc.urlRoot = url.src;
                    }
                    mc.on('error', common.error);
                    mc.fetch({
                        data: url.data || '',
                        success: function (result) {
                            callback(i, result)
                        }
                    });
                }, this);
                require([path], fn);
                return;
            }

            if (url.type === 'object') {
                url.src.fetch({
                    data: url.data || '',
                    success: function (result) {
                        callback(i, result)
                    }
                });
                return;
            }

            var result = this._fastGet(url.src, url.type || null);
            if (result) {
                callback(i, result);
                return;
            }

            $.ajax({
                url: url.src,
                data: url.data || '',
                success: function (result) {
                    if (url.type && !result.status) {
                        common.error(null, result);
                        return;
                    }
                    if (url.local) this.set(url.src, result);
                    callback(i, result);
                },
                error: function (xhr, msg, e) {
                    var info = {};
                    info.message = '系统错误:' + msg;
                    common.error(null, info);
                },
                context: this,
                dataType: url.type || null,
                async: true
            });
        },

        remove: function (key) {
            var hashkey = this.hashCode(key);
            if (this.isLocalStorage) {
                localStorage.removeItem(hashkey);
            } else {
                if (this.initDom()) {
                    this.dataDom.load(this.hname);
                    this.dataDom.removeAttribute(hashkey);
                    this.dataDom.save(this.hname)
                }
            }
        }

    }
});