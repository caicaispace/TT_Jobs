/*
* 商品列表
*/
define(['backbone', 'mu/cache', 'mu/common', 'com/table'],
    function (Backbone, cache, common, Table) {
        var view = Backbone.View.extend({

            events: {
                'click #clear-search': 'clearSearch',
                'click #refresh-list': 'refreshList',
                'click #add': 'edit',
                'click #search': 'search',
            },

            _get: {
                limit: 10,
                page: 1,
                first: 1,
            },

            initialize: function (options) {
                this._opt = options;
                this.$insertDOM = options.$insertDOM;
                _.bindAll(this, 'init');
                cache.gets([
                    {src: APP_PATH + '/template/table/list.html'},
                    {src: APP_NAME + '/table', data: this._get, type: 'ec'},
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
                    collection: this._col,
                    button: this._tpl.buttons,
                    replace: replace,
                    align: 'center',
                    isHeader: false,
                    isCheck: true,
                    width: ['other_field|50px'],
                    other_field: '操作',
                    fn: [
                        {field: 'created_at', fn: 'date|yyyy-MM-dd hh:mm:ss'},
                    ]
                });

                this.listenTo(this.table, 'tr:edit', this.edit);
                this.listenTo(this.table, 'tr:delete', this.delete);
                this.listenTo(this.table, 'tr:disabled', this.disabled);

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

            /* 添加 修改 */
            edit: function (param) {
                var model = param.model || '';
                require(['v/goods/edit'], _.bind(function (View) {
                    new View({view: this, model: model});
                }, this))
                return false;
            },

            /* 删除 */
            delete: function (param) {
                var model = param.model || '';
                common.confirm({
                    title: '确认删除此商品？',
                    content: model.get('name'),
                    callback: _.bind(function (state) {
                        if (state == false) {
                            return;
                        }
                        model.destroy({wait: true});
                    }, this),
                });

            },

            /* 禁用 */
            disabled: function (param) {
                var model = param.model || '';
                var status = model.get('status');
                var title = parseInt(status) === 1 ? '禁用' : '启用';
                common.confirm({
                    title: model.get('name'),
                    content: '确认' + title + '此商品？',
                    callback: _.bind(function (state) {
                        if (state === false) {
                            return;
                        }
                        model.save(
                            {status: parseInt(status) === 1 ? 2 : 1},
                            {wait: true, patch: true}
                        );
                    }, this),
                });
            },

            /* 清空搜索 */
            clearSearch: function () {
                this.$('input[name=search]').val('');
            },

            /* 刷新列表 */
            refreshList: function () {
                this.table.setPage(1);
            },

        });
        return view;
    }
);