define(['backbone', 'mu/cache', 'mu/common', 'com/table'],
    function (Backbone, cache, common, Table) {
        var view = Backbone.View.extend({

            events: {
                'click #clear-search': 'clearSearch'
                , 'click #refresh-list': 'refreshList'
                , 'click #add': 'edit'
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
                    {src: APP_PATH + '/template/auth/group_list.html'},
                    {src: APP_NAME + '/auth_group', data: this._get, type: 'ec'}
                ], this.init);
            },

            init: function (results) {
                this._res = results;
                this._tpl = results[0].split('||&&||');
                this._col = results[1];
                this.initConst();
                this.render();
            },

            initConst: function () {
                // 类常量
                this.constants = common.getConstants('AuthRule');
                // 类常量反射
                this.constants_re = common.getConstantsReflection('AuthRule');
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
                    status: this.constants_re.status
                    // , type: this.constants_re.type
                };

                this.table = new Table({
                    collection: this._col
                    , button: this._tpl.buttons
                    , replace: replace
                    , align: 'center'
                    , isHeader: false
                    , isCheck: true
                    , width: [
                        'other_field|50px'
                        // , 'id|100px'
                        // , 'group_name|200px'
                        , 'create_at|150px'
                        , 'update_at|150px'
                    ]
                    , other_field: '操作'
                    , fn: [
                        // { field: 'created_at', fn: 'date|yyyy-MM-dd hh:mm:ss' }
                    ]
                });

                this.listenTo(this.table, 'tr:edit', this.edit);
                this.listenTo(this.table, 'tr:delete', this.delete);

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
                var search = this.$('input[name=search]').val();
                this._get.search = search;
                this._col.fetch({data: this._get, reset: true});
                delete this._get.search;
                return false;
            },

            /* 添加 修改 */
            edit: function (param) {
                var model = param.model || '';
                require(['v/auth/group_edit'], _.bind(function (View) {
                    new View({view: this, model: model});
                }, this));
                return false;
            },

            /* 删除 */
            delete: function (param) {
                var model = param.model || '';
                common.confirm({
                    title: '确认删除？',
                    content: model.get('name'),
                    callback: _.bind(function (state) {
                        if (state === false) {
                            return;
                        }
                        model.destroy({wait: true});
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
            }

        });
        return view;
    }
);