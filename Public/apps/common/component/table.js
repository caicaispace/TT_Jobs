/**
 * 表格组件
 */
define(['backbone', 'mu/cache', 'com/tr'], function (Backbone, cache, Tr) {
    var table = Backbone.View.extend({

        className: 'no_padding',

        events: {
            'click [data-id="tr-checked-all"]': 'checkedAll',
            'click [data-table="limit"]': 'limitData',
            'click [data-custom-option]': 'customOption',
            'click [data-id="deletes"]': 'deletes'
        },

        initialize: function (options) {

            this.collection.on('reset', this.renderTableBody, this);
            this.collection.on('add', this.addTableTr, this);
            this.collection.on('add', this.caches, this);

            // this.options = options;
            this.custom_opt = options.custom_options || ''; // 自定义操作
            this.right_opt = options.right_options || ''; // 自定义操作
            this.color = options.color || 'default'; // 颜色
            this.width = options.width || ''; // td宽度 数组或对象 width: ['field|50%']   other_field 为指定其他字段的宽度
            this.isCheck = options.isCheck !== false; // 是否需要多选
            this.isHeader = options.isHeader !== false; // 是否需要多选
            this.replace = options.replace || ''; // 字段文字替换
            this.fields = this.collection._option.fields || ''; // 表头字段
            this.o_field = options.other_field || ''; // 其他表头字段
            this.align = options.align || 'center'; // 文本对齐 默认居中 	align: 'align' 或者 align: ['field|align']
            this.limit = options.limit || 10; // 默认展示数据量
            this.button = options.button || ''; // button
            this.fn = options.fn || ''; // 数据处理函数 fn: {field: 'field',fn: 'date|yyyy-MM'}] 或者  fn:[{field: 'field',fn: 'date|yyyy-MM'}]
            this.needDelete = options.needDelete === true;
            this.loadMore = options.loadMore || '';

            _.bindAll(this, 'init');
            cache.gets([{
                src: '/apps/common/template/table.html'
            }], this.init);
        },

        init: function (results) {
            this._tpl = results[0].split('||&&||');
            this.render();
        },

        render: function () {
            // td 宽度
            var width_ele = {};
            if (this.width) {
                var ele = {}, arr = [];
                this.width.map(function (i) {
                    arr = i.split('|');
                    ele[arr[0]] = arr[1];
                });
                width_ele = ele;
            }

            this.$el.html(_.template(this._tpl[1])({
                isCheck: this.isCheck,
                color: this.color,
                isHeader: this.isHeader,
                fields: this.collection._option.fields,
                o_field: this.o_field,
                needDelete: this.needDelete,
                loadMore: this.loadMore,
                width: width_ele
            }));

            // 文本对齐
            if (_.isArray(this.align)) {
                var obj = {}, arr = [];
                this.align.map(function (i) {
                    arr = i.split('|');
                    obj[arr[0]] = arr[1];
                });
                this.align = obj;
            }

            this.caches();
            this.renderTableBody();

            // 头部自定义操作
            if (this.custom_opt) this.$('#custom-options').html(this.custom_opt);
            if (this.right_opt) this.$('#right-options').html(this.right_opt);
            // this.$el.height(this.$el.height());
            return this;
        },

        /* 渲染 tbody */
        renderTableBody: function () {
            this.$tbody.html('');
            var tr = _.bind(function (model, i) {
                this.addTableTr(model, true);
            }, this);
            this.collection.each(tr);
        },

        /* tr 置入 */
        addTableTr: function (model, is_first) {

            var options = {
                model: model,
                fields: this.collection._option.fields,
                replace: this.replace,
                button: this.button,
                isCheck: this.isCheck,
                align: this.align,
                fn: this.fn
            };

            var tr = new Tr(options);
            this.listenTo(tr, 'tr', this.trEvents);

            if (is_first === void 0) {
                this.$tbody.prepend(tr.render().el);
                tr.$el.css({
                    background: "#4cae4c",
                    opacity: 0
                }).animate({opacity: 1}, 1000,
                    function () {
                        $(this).css({
                            background: ""
                        })
                    }
                );
            } else {
                this.$tbody.append(tr.render().el);
            }
        },

        /* 缓存 */
        caches: function () {

            // ele 缓存
            this.$tbody = this.$('tbody');
            this.$checkboxs = this.$('[data-tr="checked"]'); // 全选 反选
            this.$current_limit = this.$('[data-table="limit"]').eq(0).find('span'); // 当前限制数据条数

            // data 缓存

            // 其他 缓存
            if (this.align !== 'center' && typeof this.align !== 'object') {
                this.$('th,td').attr('class', 'text-' + this.align);
            }
        },

        /* tr 事件 */
        trEvents: function (type, model) {
            this.trigger('tr:' + type, model);
        },

        /* 自定义操作 */
        customOption: function (e) {
            var $ele = $(e.currentTarget);
            var type = $ele.attr('data-custom-option');
            var param = {
                $ele: $ele,
                $el: this.$el
            };
            this.trigger(type, param);
        },

        /* tr 全选 */
        checkedAll: function (e) {
            var status = e.currentTarget.checked;
            this.collection.each(function (model) {
                model.__isChecked__ = status;
            });
            this.$checkboxs.each(function () {
                this.checked = status;
            });
        },

        /* 每页数据 */
        limitData: function (e) {
            var $this = $(e.currentTarget);
            var limit = $this.find('span').html();
            var len = this.collection.length;
            this.$current_limit.html(limit);

            // 减少
            if (limit < len) {
                this.collection.limit(this.collection, len - limit, this.caches)
                // 重新拉取
            } else if (limit === this.limit) {
                this.collection.fetch({reset: true});
                // 增加
            } else {

                var fn = _.bind(function (data) {
                    var collection = data[0];
                    var _this = this;
                    var st = setTimeout(function () {
                        _this.collection.push(collection.shift());
                        if (typeof collection !== 'undefined' || collection.length > 0)
                            setTimeout(arguments.callee, 10);
                    }, 10);
                }, this);

                var url = this.collection.url + '?cl=' + len + '&l=' + limit;
                cache.gets([{
                    src: url,
                    type: 'ec'
                }], fn);
            }
            len = limit;
        },

        // /* 多选删除 */
        // deletes: function(){
        // 	var  fn = _.bind(function(){

        // 		var confirm = _.bind(function() {
        // 			this.collection.deletes(this.collection);
        // 		}, this);

        // 		swal({
        // 			title: "确认删除选择的数据吗?",
        // 			type: "warning",
        // 			showCancelButton: true,
        // 			confirmButtonClass: "btn-danger",
        // 			confirmButtonText: "确认",
        // 			closeOnConfirm: true
        // 		}, confirm);

        // 	}, this);

        // 	require(['sweetAlert'], fn);
        // },

        /* 初始化分页 */
        showPage: function (page_get) {
            // this._get = _.clone(page_get);
            this._get = page_get;
            this.collection.on('reset', this.resetPage, this);
            require(['com/page', 'mu/common'], _.bind(function (Page, common) {
                this._page_model = common.getModel({
                    limit: this._get.limit,
                    total: this.collection._page.total
                });
                this.resetPage();
                var _page = new Page({model: this._page_model});
                this.listenTo(_page, 'clickPage', this.setPage);
                this.$el.append(_page.el);
            }, this));
        },

        /* 分页重置 */
        resetPage: function () {
            this._page_model.set('page', this.collection._page.current);
            if (this.collection._page.total) {
                this._page_model.set('total', this.collection._page.total);
            }
        },

        /* 分页 */
        setPage: function (current) {
            if (current > 1) {
                delete this._get.first;
            } else {
                this._get.first = 1;
            }
            this._get.page = current;
            this.collection.fetch({data: this._get, reset: true});
        },

        /* 刷新 */
        refres: function (current) {
            this.collection.fetch({reset: true});
        }

    });
    return table;
});