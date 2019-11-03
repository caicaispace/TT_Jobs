define(['backbone', 'mu/cache', 'mu/common', 'com/table'],
    function (Backbone, cache, common, Table) {
        var view = Backbone.View.extend({

            events: {
                'click #clear-search': 'clearSearch'
                , 'click #refresh-list': 'refreshList'
                , 'click #add': 'edit'
                , 'click #search': 'search'
                , 'click [task-group-id]': 'darenTagFilter',
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
                    {src: APP_PATH + '/template/task/list.html'},
                    {src: APP_NAME + '/task', data: this._get, type: 'ec'},
                    {src: APP_NAME + '/task_group?limit=50&page=1', type: 'json'},
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
                this.taskGroupDropdown();
                this.$insertDOM.html(this.el);
                return this;
            },

            /* 列表 */
            list: function () {

                var users_hash = {};
                APP.users.map(function (user) {
                    users_hash[user.id] = user['zh_username'];
                });

                var replace = {
                    status: APP.hashs.status,
                    user_id: users_hash,
                };

                this.table = new Table({
                    collection: this._col
                    , button: this._tpl.buttons
                    , replace: replace
                    , isHeader: false
                    , isCheck: true
                    , width: [
                        'other_field|50px'
                        , 'id|60px'
                        // , 'task_name|550px'
                        , 'description|150px'
                        , 'user_id|100px'
                        , 'cron_spec|100px'
                        , 'execute_times|100px'
                        , 'prev_time|150px'
                        , 'create_at|150px'
                        , 'update_at|150px'
                        , 'status|50px'
                    ]
                    , align: [
                        'task_name|left'
                        , 'description|left'
                    ]
                    , other_field: '操作'
                    , fn: [
                        {field: 'prev_time', fn: 'date|yyyy-MM-dd hh:mm:ss'}
                    ]
                });

                this.listenTo(this.table, 'tr:edit', this.edit);
                this.listenTo(this.table, 'tr:patch', this.patch);
                this.listenTo(this.table, 'tr:copy', this.copy);
                this.listenTo(this.table, 'tr:test', this.test);

                if (!this.$list) {
                    this.table.showPage(this._get);
                    this.$list = this.$('#list');
                }
                this.$list.html(this.table.el);
            },

            taskGroupDropdown: function () {
                var $dropdown = this.$('#task-group-dropdown');
                var tpl = this._tpl.taskGroupDropdown;
                var data = this._res[2];
                var html = _.template(tpl)(data);
                $dropdown.html(html);
            },

            darenTagFilter: function (e) {
                var $ele = $(e.currentTarget);
                var group_id = $ele.attr('task-group-id');
                this._get.first = 1;
                this._get.page = 1;
                if (group_id > 0) {
                    this._get.group_id = group_id;
                } else {
                    delete this._get.group_id
                }
                this._col.fetch({data: this._get, reset: true});
            },

            /* 搜索 */
            search: function () {
                this._get.first = 1;
                this._get.page = 1;
                this._get.search = this.$('input[name=search]').val();
                this._col.fetch({data: this._get, reset: true});
                this.table.resetPage();
                delete this._get.search;
                return false;
            },

            /* 添加 修改 */
            edit: function (param) {
                var model = param.model || '';
                require(['v/task/edit'], _.bind(function (View) {
                    new View({view: this, model: model});
                }, this));
                return false;
            },

            patch: function (param) {
                var model = param.model || '';
                var $ele = param.$ele || '';
                var data = JSON.parse($ele.attr('data-data'));
                common.confirm({
                    title: model.get('task_name'),
                    content: '确认' + $ele.attr('title') + '吗？',
                    callback: _.bind(function (state) {
                        if (state === false) {
                            return;
                        }
                        model.save(
                            data,
                            {wait: true, patch: true}
                        );
                    }, this)
                });
            },

            copy: function (param) {
                var model = param.model || '';
                var $ele = param.$ele || '';
                var data = model.toJSON();

                data = {
                    task_name: data.task_name + '_copy',
                    description: data.description,
                    cron_spec: data.cron_spec,
                    command: data.command,
                    group_id: data.group_id,
                    single: data.single,
                    server_id: data.server_id,
                    task_type: data.task_type,
                    timeout: data.timeout,
                    user_id: APP.session.id,
                    execute_times: 0,
                    prev_time: 0,
                };

                common.confirm({
                    title: model.get('task_name'),
                    content: '确认拷贝此任务吗？',
                    callback: _.bind(function (state) {
                        if (state === false) {
                            return;
                        }
                        this._col.create(data, {wait: true});
                    }, this)
                });
            },

            /* test */
            test: function (param) {
                var model = param.model || '';
                var command = model.get('command');
                var data = {
                    'command': command
                };
                cache.get(APP_NAME + '/task/run_test', data, _.bind(function (ret) {
                    require(['com/dialog'], _.bind(function (Dialog) {
                        this._Dialog = new Dialog({
                            title: '测试输出',
                            type: 'close',
                            size: 'wide',
                            html: ret.info,
                        });
                    }, this));
                }, this));
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