
//  calendar.js
//  version : 0.1
//  author : Cai Yang
//  last updated at: 2015-08-27
//  www.xxxxxx.com

;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery', 'plugins/moment/moment'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS. Register as a module
        module.exports = factory(require('jquery'), require('plugins/moment/moment')); 
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($, moment){

    var View, bind, backboneView;

    $.fn.calendar = function(opt){

        if (!opt) opt = {};

        // 默认配置
        opt = $.extend(true,
        {

            week: '',
            start: '',
            end: ''

        },opt);

        opt.$this = this;
        new bind(View, backboneView()) (opt);
        return this;
    };

    backboneView = function(){
        view = {
            tagName : 'div',
            className : 'calendar',
            events : {
                'click [data-opt="month_click"]' : 'monthClick',
                'click [data-opt="day_click"]' : 'dayClick',
                'click [data-opt="status_click"]' : 'statusClick',
                'click #custom-current' : 'customCurrent'
             },

            initialize : function(options){
                this.options = options;
                this.render();
            },

            render : function(date){
                var week  = this.options.week;
                var data  = this.options.data;
                var start = moment(parseInt(this.options.start)*1000).format('YYYYMMDD');
                var end   = moment(parseInt(this.options.end)*1000).format('YYYYMMDD');
                var date             = date ? date : moment().format('YYYY-MM');
                var month_days       = this.getDateNum('md',date);
                var last_month       = this.getDateNum('lm',date);
                var next_month       = this.getDateNum('nm',date);
                var week_month_day1  = this.getDateNum('wmd',date);
                var week_month_day30 = moment(date+'-'+month_days).format('e');
                var last_month_days  = this.getDateNum('md',last_month + '-01') - week_month_day1;

                var cal = {
                    date : date,
                    week : week,
                    timeStart : start,
                    timeEnd : end,
                    last_month : last_month,
                    next_month : next_month,
                    month_days : month_days,
                    last_month_days : last_month_days,
                    week_month_day1 : week_month_day1,
                    week_month_day30 : week_month_day30
                }
                this.$el.html(this.tpl({cal:cal, data:data, moment:moment}));
                this.eventsListen();
                return this;
            },

            /**
             * 月份切换
             * @param  {[type]} e [description]
             * @return {[type]}   [description]
             */
            monthClick : function(e){
                var date = $(e.currentTarget).attr('data-cal');
                this.render(date);
            },

            /**
             * 点击哪一天
             * @param  {[type]} e [description]
             * @return {[type]}   [description]
             */
            dayClick : function(e){
                var $dom = $(e.currentTarget);
                $dom.css({background:'#FFF3C4'}).siblings().css({background:''});
                var date  = $dom.attr('data-date');
                var month = $dom.attr('data-month');
                var day   = $dom.attr('data-day');
                console.log(date,month,day);
                // this.trigger('day_click',date);
            },

            statusClick : function(e){

            },

            /**
             * 获取日期
             * @param  {string} type 日期类型
             * @param  {string} date 2015-06
             * @return {[type]}      [description]
             */
            getDateNum : function(type,date){

                var date   = date ? date : moment().format('YYYY-MM-DD');
                var arr    = date.split('-');
                var year   = arr[0];
                var month  = arr[1];
                var day    = arr[3];
                var num;

                switch (type){
                    case 'wmd': //week day is this date of the month first day (对应月份的第一天星期几)
                        num = moment(year+'-'+month+'-01').format('e');
                        break;
                    case 'md': // how many days this date of month （对应月份有多少天）
                        num = moment(date).endOf('month').format('DD');
                        break;
                    case 'lm': // this date of the last month （上个月）
                        num = moment(date).add(-1,'month').format('YYYY-MM');
                        break;
                    case 'nm': // this date of the next month （下一月）
                        num = moment(date).add(1,'month').format('YYYY-MM');
                        break;
                    default: // now date
                        num = moment().format('YYYY-MM');
                }
                return num;
            },

            tpl : function(params){
                var cal      = params.cal;
                var data     = params.data;
                var now_date = new Date().format("yyyyMMdd");
                var day,full_date,date_week;
                var tpl = '<div class="cal_operation">'
                            +'<div class="col-md-2 month_last " data-opt="month_click" data-cal='+cal.last_month+' title="'+ cal.last_month +'" > < </div>'
                            +'<div class="col-md-8 month_show"> <span id="cal_date">'+ cal.date +'</span> 月 </div>'
                            +'<div class="col-md-2 month_next " data-opt="month_click" data-cal="'+ cal.next_month +'" title="'+ cal.next_month +'" > > </div>'
                         +'</div>';

                    tpl+='<div class="cal_lg">'
                            +'<div class="cal_head hidden-xs">'
                                +'<div>周 一</div>'
                                +'<div>周 二</div>'
                                +'<div>周 三</div>'
                                +'<div>周 四</div>'
                                +'<div>周 五</div>'
                                +'<div>周 六</div>'
                                +'<div>周 日</div>'
                            +'</div>'
                            +'<div class="cal_body">';
                            for(var i=1 ; i <= cal.week_month_day1; i++) {
                                tpl+='<div class="cal_none col-md-2 hidden-xs">'+ (++cal.last_month_days) + '</div>';
                            }

                            for(var i=1 ; i<= cal.month_days; i++) {

                                day = i <= 9 ? '0'+i : i
                                full_date = cal.date.replace(/-/g,'') + day
                                date_week = parseInt(moment(cal.date+'-'+day).format('e')) + 1

                                if(full_date >= cal.timeStart && full_date >= now_date && full_date <= cal.timeEnd && cal.week['wd_'+date_week] == '1' ) {
                                    tpl+='<div data-opt="day_click" data-day="'+day+'" data-month="'+cal.date+'" data-date="'+cal.date+'-'+day+'">'
                                        +'<strong>'+i+'</strong>';
                                        if(full_date == now_date ) { tpl+='<span style="color:#ff4400;float: right">今天</span>' }
                                    tpl+='<br>'
                                        +'<span>'
                                            +'景:￥ '+data.cost_price+'<br>'
                                            if (data.proxy_price != "--")
                                                tpl+= "代:￥ "+data.proxy_price;
                                            else
                                                tpl+="";
                                            tpl+='<span style="color: #4cae4c;float: left">';
                                            if(typeof data.special_price[full_date] != "undefined")
                                                tpl+="特";
                                            else 
                                                tpl+="";
                                            tpl+='</span>'
                                            +'<span style="color: #00A0BD;float: right"></span>'
                                        +'</span>'
                                    +'</div>'
                                }else{
                                    tpl+='<div>'
                                        +'<strong> '+i+' </strong>';
                                        if(full_date === now_date ) {tpl+='<span style="color:#ff4400;float: right">今天</span>'};
                                    tpl+='<br>'
                                        +'<span style="color: #959999;">'
                                            +'无<br>'
                                        +'</span>'
                                    +'</div>';
                                }
                            }

                            for(var i = 1 ; i < 7-cal.week_month_day30; i++) {
                                tpl+='<div class="cal_none hidden-xs">'+i+'</div>';
                            }

                    tpl+='</div></div>';
                return tpl;
            }

        }
        return view;
    };

    View = function(options){
        var e, arr;
        var events = this.events || null
        var $this  = options.$this;
        var params = {
                "id": this.id || ""
            };
        this.eventsListen = function(){
            if (events) {
                for(e in events){
                    arr = e.split(/\s+/);
                    this.$el.find( arr[1] ).on( arr[0], bind(this[events[e]], this) );
                }
            };
        }
        $this.html( $(this.tagName, params) );
        $this.attr({'class':this.className})
        this.$el = $this;
        this.initialize(options);
    };

    bind = function(fn, context){
        return function(){
            return fn.apply(context, arguments);
        }
    };

    Date.prototype.format = function(format){ 
        var o = { 
            "M+" : this.getMonth()+1, //month 
            "d+" : this.getDate(), //day 
            "h+" : this.getHours(), //hour 
            "m+" : this.getMinutes(), //minute 
            "s+" : this.getSeconds(), //second 
            "q+" : Math.floor((this.getMonth()+3)/3), //quarter 
            "S" : this.getMilliseconds() //millisecond 
        } 

        if(/(y+)/.test(format)) { 
            format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
        } 

        for(var k in o) { 
            if(new RegExp("("+ k +")").test(format)) { 
                format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length)); 
            } 
        } 
        return format; 
    }
}));
