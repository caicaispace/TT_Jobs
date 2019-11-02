window.APPS_PATH = '../../apps/';
// window.APPS_PATH = '../../apps-build/';
window.APP_NAME = 'admin';
window.APP_PATH = window.APPS_PATH + window.APP_NAME;

window.APP = {
    APP_PATH: APP_PATH,
    TPL_NAME: 'default', // hash 数据
    currentView: null, //当前视图
    debug: true, //调试模式
    session: {}, //用户session信息
    hashs: {}, // hash 数据
};

window.TEST_DATA = APP_PATH + '/test_data';

require.config({

    baseUrl: 'base/js',

    paths: {

        /************* Base *************/
        // backbone: 'backbone-1.2.1',
        bootstrap_dialog: '/base/plugins/bootstrap-dialog/bootstrap-dialog.min',
        bootstrap_daterangepicker: '/base/plugins/bootstrap-daterangepicker/bootstrap-daterangepicker',
        bootstrap_daterangepicker_zh: '/base/plugins/bootstrap-daterangepicker/bootstrap-daterangepicker.zh-CN',
        // bootstrap_editable:  'plugins/bootstrap-editable',
        kindeditor: '/base/plugins/kindeditor/kindeditor-all-min',
        kindeditor_zh: '/base/plugins/kindeditor/zh_CN',
        // raphael:  'plugins/raphael',
        // morrisjs:  'plugins/morris',
        moment: '/base/plugins/moment/moment.min',
        // moment_cn:'plugins/moment/moment_zh-cn',
        jquery_file_upload: '/base/plugins/bootstrap-jquery.fileupload/bootstrap-jquery.fileupload',
        // calendar:  'plugins/jquery.calendar',
        // dataTables:  'plugins/jquery.dataTables',
        nprogress: '/base/plugins/nprogress/nprogress.min',
        jquery_form: '/base/plugins/jquery-extends/jquery.form',
        jquery_region: '/base/plugins/jquery-extends/jquery.region',
        /************* Tools *************/

        async: '/base/plugins/require/async',
        domReady: '/base/plugins/require/domReady',
        jquery_bmap: '/base/plugins/jquery-extends/jquery.baidu.map',

        jquery_fullscreen: '/base/plugins/jquery-extends/jquery.fullscreen',
        jquery_sortable: '/base/plugins/jquery-extends/jquery.sortable',

        /************* Template *************/

        notification: '/tpls/' + APP.TPL_NAME + '/js/notification',
        metisMenu: '/tpls/' + APP.TPL_NAME + '/plugins/metisMenu/metisMenu',
        raphael: '/tpls/' + APP.TPL_NAME + '/plugins/raphael/raphael.min',
        sb_admin_2: '/tpls/' + APP.TPL_NAME + '/js/sb-admin-2',
        morrisjs: '/tpls/' + APP.TPL_NAME + '/plugins/morrisjs/morris.min',

        app: APP_PATH,
        v: APP_PATH + '/views',
        c: APPS_PATH + 'common/collections',
        m: APPS_PATH + 'common/models',
        mu: APPS_PATH + 'common/module',
        com: APPS_PATH + 'common/component'
    },

    packages: [
        // {
        //     name: 'echarts',
        //     location: '/js/echarts',
        //     main: 'echarts'
        // },
        // {
        //     name: 'zrender',
        //     location: '/js/zrender', // zrender与echarts在同一级目录
        //     main: 'zrender'
        // }
    ],

    shim: {
        backbone: ['underscore', 'jquery'],
        bootstrap: ['jquery', 'enquire', 'bootstrap_dialog'],
        bootstrap_editable: ['moment'],
        bootstrap_daterangepicker: ['moment'],
        'morrisjs': {
            'deps': ['jquery', 'raphael'],
            'exports': 'Morris',
            'init': function ($, Raphael) {
                window.Raphael = Raphael;
            }
        },
    }
});

require(['jquery', 'bootstrap', 'backbone', 'underscore', 'store'], function () {

    require([
        'mu/functions',
        'mu/serializeObject',
        'mu/hashs',
    ], function(){
        // $.ajax({
        //     url:"admin",
        //     data: {__act:'init'},
        //     dataType:'json'
        // })
        // .done(function (res) {
        //     console.log(res['info']['hashs']);
        //     Object.assign(window.APP.hashs, res['info']['hashs']);
        // })
    });

    require([APP.APP_PATH + '/router']);

    // /* jquery_flot */
    // require(['jquery_flot'], function(){
    //     require(
    //         [
    //             'plugins/flot/excanvas',
    //             'plugins/flot/jquery.flot.pie',
    //             'plugins/flot/jquery.flot.resize',
    //             'plugins/flot/jquery.flot.time',
    //             'plugins/flot/jquery.flot.tooltip'
    //         ]
    //     );
    // });

    // /* 当前用户信息 */
    // var fun = _.bind(function(data){
    //     this.APP.session = data.info;
    // }, window);
    // $.get('/api/contacts/contactsInfo',{},fun, 'json');


});
