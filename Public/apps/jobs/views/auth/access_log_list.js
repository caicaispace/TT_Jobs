define(['backbone', 'mu/cache', 'mu/common', 'com/table'],
    function (Backbone, cache, common, Table) {
        var view = Backbone.View.extend({

            events: {
                'click #clear-search': 'clearSearch'
                , 'click #refresh-list': 'refreshList'
                , 'click #search': 'search'
            },

            _get: {
                limit: 15
                , page: 1
                , first: 1
            },

            initialize: function (options) {
                this._opt = options;
                this.$insertDOM = options.$insertDOM;
                _.bindAll(this, 'init');
                cache.gets([
                    {src: APP_PATH + '/template/auth/access_log_list.html'},
                    {src: APP_NAME + '/auth_access_log', data: this._get, type: 'ec'}
                ], this.init);
            },

            init: function (results) {
                this._res = results;
                this._tpl = results[0].split('||&&||');
                this._col = results[1];

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
                this.list();
                this.$insertDOM.html(this.el);
                return this;
            },

            /* 列表 */
            list: function () {

                var replace = {
                    status: APP.hashs.status,
                };

                this.table = new Table({
                    collection: this._col
                    , button: this._tpl.buttons
                    , replace: replace
                    , isHeader: false
                    , isCheck: true
                    , width: [
                        'other_field|50px'
                        , 'uid|100px'
                        , 'access_path|500px'
                        , 'create_at|150px'
                    ]
                    , align: [
                        'access_path|left'
                        , 'access_data|left'
                    ]
                    , other_field: '操作'
                    , fn: [
                        // { field: 'create_at', fn: 'date|yyyy-MM-dd hh:mm:ss' }
                    ]
                });

                if (!this.$list) {
                    this.table.showPage(this._get);
                    this.$list = this.$('#list');
                }
                this.$list.html(this.table.el);
            },

            /* 搜索 */
            search: function () {
                this._get.first = 1;
                this._get.page = 1;
                this._get.search = this.$('input[name=search]').val();
                this._col.fetch({data: this._get, reset: true});
                delete this._get.search;
                return false;
            },

            /* 清空搜索 */
            clearSearch: function () {
                this.$('input[name=search]').val('');
            },

            /* 刷新列表 */
            refreshList: function () {
                this.table.setPage(1);
            }

        });
        return view;
    }
);