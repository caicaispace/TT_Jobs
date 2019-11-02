/**
 * hash 数据
 */
define(function () {

    window.APP.hashs = {

        files: {},

        status: {
            0: '<span style="color:red">停用</span>',
            1: '<span style="color:green">启用</span>'
        },

        member: {
            is_speech: {
                0: '<span style="color:red">是</span>',
                1: '<span style="color:green">否</span>',
            },
            is_improve_info: {
                0: '否',
                1: '是'
            },
        },

        models: {
            constants: {

            },
            constants_reflection: {
                AuthRule: {
                    type: {
                        1: '时时认证',
                        2: '登录认证',
                    },
                    status: {
                        0: '<span style="color:red">禁用</span>',
                        1: '<span style="color:green">启用</span>'
                    },
                },
            }
        },

        img: {
            portrait: '/img/portrait.jpg',
        },

        daterangepicker: {
            "format"     : 'YYYY/MM/DD HH:mm:ss'
            , "applyLabel": '确认'
            , "cancelLabel": '取消'
            , "fromLabel": '从'
            , "toLabel": '到'
            , "weekLabel": 'W'
            , "customRangeLabel": '选择日期'
            , "daysOfWeek": ["日", "一", "二", "三", "四", "五", "六"]
            , "monthNames": ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"]
        }
    };
    return window.APP.hashs;
});
