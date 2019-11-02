define(['backbone', 'mu/cache', 'mu/common'], function(Backbone, cache, common) {
    var view = Backbone.View.extend({

        tagName: 'div',
        className: 'container-fluid',

        initialize: function(options) {
            this.$insertDOM = options.$insertDOM;
            _.bindAll(this, 'init');
            cache.gets([
                {src: APP_PATH + '/template/statistics/dashboard.html'},
                {src: APP_NAME + '/statistics?__act=dashboard', type: 'json'},
            ], this.init);
        },

        init: function(results) {
            this._res = results;
            this._tpl = results[0].split('||&&||');
            this._data = results[1]['info'];
            this.render();
        },

        render: function() {

            var registers = this._data['registers'];

            var total = this._data['registers']['total'];
            var week_total    = registers['week_total'];
            var month_total   = registers['month_total'];

            var list          = registers['thirty_day_list'];
            var man_total     = registers['man_total'];
            var woman_total   = registers['woman_total'];
            var ios_total     = registers['ios_total'];
            var android_total = registers['android_total'];
            var wx_total      = registers['wx_total'];
            var qq_total      = registers['qq_total'];
            var report_user_total = registers['report_user_total'];

            var today_total = 0;
            var yesterday_total = 0;
            if (list.length > 0) {
                today_total = list[0]['total'];
                yesterday_total = list[1]['total'];
            }

            var data = {
                'total': total,
                'today_total': today_total,
                'yesterday_total': yesterday_total,
                'week_total': week_total,
                'month_total': month_total,
                'man_total':man_total,
                'woman_total':woman_total,
                'ios_total':ios_total,
                'android_total':android_total,
                'wx_total':wx_total,
                'qq_total':qq_total,
                'report_user_total':report_user_total
            };
            var html = _.template(this._tpl[1]) (data);
            this.$el.html(html);
            this.$insertDOM.html(this.el);
            require(['morrisjs'], _.bind(this.MorrisDatas, this));
            return this;
        },

        MorrisDatas: function() {

            // 日活
            Morris.Area({
                element: 'day-activity-list',
                data: this._data['day_activity_list'],
                xkey: 'date',
                ykeys: ['total'],
                labels: ['日活'],
                pointSize: 2,
                hideHover: 'auto',
                resize: true
            });

            // 月活
            Morris.Area({
                element: 'month-activity-list',
                data: this._data['month_activity_list'],
                xkey: 'date',
                ykeys: ['total'],
                labels: ['月活'],
                pointSize: 2,
                hideHover: 'auto',
                resize: true
            });

            var registers = this._data['registers'];

            var thirty_day_list  = registers['thirty_day_list'];
            var man_total     = registers['man_total'];
            var woman_total   = registers['woman_total'];
            var ios_total     = registers['ios_total'];
            var android_total = registers['android_total'];
            var wx_total      = registers['wx_total'];
            var qq_total      = registers['qq_total'];

            // 日新
            Morris.Area({
                element: 'register-area-chart',
                data: thirty_day_list,
                xkey: 'date',
                ykeys: ['man', 'woman'],
                labels: ['男', '女'],
                pointSize: 2,
                hideHover: 'auto',
                resize: true
            });

            // 性别比例
            var total = ((+man_total) + (+woman_total)) / 100;
            var man_scale = Math.round(man_total/total);
            var woman_scale = Math.round(woman_total/total);

            Morris.Donut({
                element: 'register-donut-chart',
                data: [
                    {label: '女', value: woman_scale},
                    {label: '男', value: man_scale},
                ],
                resize: true
            });

            // 设备比例
            var total = ((+ios_total) + (+android_total)) / 100;
            var ios_scale = Math.round(ios_total/total);
            var android_scale = Math.round(android_total/total);

            Morris.Donut({
                element: 'device-type-donut-chart',
                data: [
                    {label: 'IOS', value: ios_scale},
                    {label: 'Android', value: android_scale}
                ],
                resize: true
            });

            // 第三方登录比例
            var total = ((+wx_total) + (+qq_total)) / 100;
            var wx_scale = Math.round(wx_total/total);
            var qq_scale = Math.round(qq_total/total);

            Morris.Donut({
                element: 'third-party-donut-chart',
                data: [
                    {label: '微信', value: wx_scale},
                    {label: 'QQ', value: qq_scale}
                ],
                resize: true
            });


        },
    });
    return view;
});
