/*

 fileUpload.js
 version : 1.0
 author : ZhiFei Zhang、 Cai Yang
 last updated at: 2017-09-12
 www.xxxxxx.com

*/
;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery', 'jquery_form'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS. Register as a module
        module.exports = factory(require('jquery'), require('/base/plugins/jquery-extends/jquery.form.js')); 
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($, form){

    var View, bind, _bindAll,  GUID, GUIDx4;

    $.fn.fileUpload = function(opt){

        if (!opt) opt = {};

        // 默认配置
        opt = $.extend(true,
        {
            files: '',                      // 初始文件
            preview_url: '',                // 预览地址
            url: '/api/file/create',        // 上传地址
            del_url: '/api/file/delete',    // 删除地址
            name: 'images',                 // 键名标识名
            thumb: '200x200',               // 缩略图大小
            limit_num: 2,                   // 数量限制
            limit_size: 2*1048576,          // 大小限制
            prompt: '',                     // 默认提示文本
            success: '',                    // 上传成功 param [ arr ids ] 当前文件编号 
            delete: '',                     // 确认删除 param [ str file_name ] 当前文件名，[ str index ] 当前文件的索引
            delSuccess: ''                  // 删除成功 param [str index] 当前文件的索引 
        },opt);

        opt.$el = this;
        new View().init(opt);
        return this;

    }

    View = function(){

        return {

            root: {},

            init: function(options){

                this.opt = options;
                this.$el = options.$el;
                this.root.GUID = GUIDx4();

                // 计数器
                this.root.selected_num       = 0;  // 已经选取文件数
                this.root.uploaded_num       = 0;  // 已经上传文件数
                this.root.sync_num           = 0;  // 同步文件数
                this.root.current_ids        = []; // 正在上传文件 id
                this.root.files              = [];       // 已选择的文件
                this.root.currentFilesLength = 0; // 当前选取的文件数量

                // 初始化页面
                this.render();

                // DOMs
                this.root.$fui       = this.$el.find('#file-upload-input'+this.root.GUID);
                this.root.$fus       = this.$el.find('#file-upload-show'+this.root.GUID);
                this.root.$fuf       = this.$el.find('#file-upload-files'+this.root.GUID);
                this.root.$fup       = this.$el.find('#file-upload-prompt'+this.root.GUID);
                this.root.$fup2      = this.$el.find('#file-upload-prompt2'+this.root.GUID);
                this.root.$files     = []; // 已选择文件 dom
                this.root.$form      = '';
                this.root.$fileInput = '';
                this.root.$fud       = this.tpl(5) ();

                // 初始上传 input
                this.root.$fuf.append( this.root.$fud );

                // 展示初始文件
                if (this.opt.files && this.opt.files != '') {
                    this.fileShow();
                    this.root.$fup.remove();
                }

                return this;
            },

            /**
             * 初始化页面
             */
            render: function(){

                this.$el.html(this.tpl(0)());
                var $dom = this.$el.find('#file-upload-dom' + this.root.GUID);

                setTimeout(bind(function () {
                    this.root.$form = this.tpl(4)();
                    this.root.$fileInput = this.$el.find('#file-upload-input' + this.root.GUID);
                    this.root.$fileInput.change(bind(this.fileChange, this));
                    // console.log(this.root);
                    this.$el.append(this.tpl(3)(this.opt.prompt));
                }, this), 100);

                return this;
            },

            /**
             * 选取文件
             */
            fileChange: function(){

                var status = true;
                var _this = this;

                this.root.$fup.slideUp(500);
                this.root.files.push({ el: this.root.$fileInput });
                var files = this.root.$fileInput[0].files;
                var length = this.root.currentFilesLength = files.length;

                // 是否超出限制
                var limit = this.opt.limit_num;
                if ((this.root.sync_num + length) > limit) {
                    this.inputReset();
                    this.dataReset();
                    return this.prompt('此处最多只能上传 ' + limit + ' 个文件 !!! ', 'info');
                };

                // 预览、验证文件
                var info;
                var $file;
                var i = 0;
                for (i; i < length; i++) {

                    info = this.fileVerify(files[i]);

                    if (!info) {
                        this.inputReset();
                        this.dataReset();
                        status = false;
                        break; return;
                    }

                    this.root.sync_num += 1;
                    this.root.selected_num += 1;
                    this.root.current_ids.push(this.root.selected_num);
                    info = this.getFileInfo(info);

                    $file = this.tpl(1)(info.url, this.root.selected_num, '.');
                    $file.file_name = info.name.substr(0, 10);
                    $file.find('.file-upload-delete').click(function () {
                        _this.fileDelete($(this), $(this).parent().index());
                    });
                    this.root.$files.push($file);
                    this.root.$fus.append($file);
                }

                // 发送参数
                var upParam = {
                    'thumb': this.opt.thumb
                }

                if(status == true) {
                    // toDo 必须延迟，为什么？？？？？
                    setTimeout(bind(function () {
                        this.fileSubmit(upParam);
                    }, this), 100);
                }

                this.inputReset();

                // 调整 上传选取文件 input 的高度

                var _this = this;
                setTimeout(function () {
                    var height = _this.$el.find('#file-upload-dom' + _this.root.GUID).height();
                    if (height > 200) {
                        _this.$el.find('#file-upload-input' + _this.root.GUID).height(height);
                        _this.root.$fus.css('margin-top', - height);
                        _this.root.$fus.css('height', height);
                    }
                }, 100)

            },

            /**
             * 显示初始文件
             */
            fileShow: function(){


                this.root.$fuf.html(''); // 清除默认文件保存表单

                files = this.isString(this.opt.files)
                            ? this.opt.files.split(',')
                            : this.opt.files;

                var id;
                var thumb;
                var $dom;
                var _this = this;
                var i = 0;
                for (i; i < files.length; i += 1) {
                    id = i + 1;
                    this.root.selected_num = id;
                    this.root.uploaded_num = id;
                    this.root.sync_num = id;
                    thumb = this.fileVerify(files[i]);
                    thumb = this.opt.preview_url + thumb;
                    $dom = this.tpl(1)(thumb, id, '( √ )');
                    $dom.find('.file-upload-delete').click(function () {
                        _this.fileDelete($(this), $(this).parent().index());
                    });
                    $dom.appendTo(this.root.$fus);
                    this.root.$fuf.append(this.tpl(2)(id, this.opt.name, files[i]));
                }
            },

            /**
             * 提交保存
             */
            fileSubmit: function(data){

                document.body.appendChild(this.root.$form[0]);
                this.root.$form.html('');
                var file = this.root.files.shift();

                if (!file)
                    return false;

                file.el.appendTo(this.root.$form);

                var files_length = this.root.currentFilesLength;

                var success = bind(this.fileSaveSuccess, this);
                var error = bind(this.fileSaveError, this);
                var options = { dataType: 'json', data: data };

                options.success = bind(function (data) {
                    this.fileSubmit();
                    if (data.status && success) return success(data, file.id);
                    if (!data.status && error) return error(data, file.id);
                }, this)

                options.error = bind(function (info) {
                    this.fileSubmit();
                    if (error) error(info, file.id);
                }, this)

                // 上传进度
                options.uploadProgress = bind(this.fileSaveProgress, this);

                this.root.$form.ajaxSubmit(options);
            },

            /**
             * 上传成功
             * @param  {obj} data
             * @param  {int} id
             * @return {}
             */
            fileSaveSuccess: function(data, id){

                this.root.$fud.remove(); // 删除默认文件保存表单

                var height;
                var datas = data.info;
                var $files = this.root.$files;

                var i = 0;
                for (i; i < datas.length; i += 1) {
                    this.root.uploaded_num += 1;
                    $files[i].find('.file-upload-filename')// 显示文件名
                        .html($files[i].file_name);
                    this.root.$fuf.append(this.tpl(2)(this.root.uploaded_num, this.opt.name, datas[i].path));// 添加隐藏 input
                }

                // success callback
                if (this.isFunction(this.opt.success))
                    this.opt.success(this.root.current_ids);

                // console.count();
                // console.group();
                // console.log(this);
                // console.log(data, id);
                // console.groupEnd();
                // console.group();
                // console.log('已选取-->', this.root.selected_num);
                // console.log('已上传-->', this.root.uploaded_num);
                // console.log('同步-->', this.root.sync_num);
                // console.log('正在上传-->', this.root.current_ids);
                // console.log('已选择文件-->', this.root.$files);
                // console.log('当前选取的文件数量-->',  this.root.currentFilesLength);
                // console.groupEnd();

                this.root.$form.html(''); //  清空 form
                this.dataReset(); // 清空数据

            },

            /**
             * 上传失败
             * @param  {obj} data 
             * @param  {int} id   
             * @return {}      
             */
            fileSaveError: function(data, id){

                var guid = this.root.GUID;
                var ids = this.root.current_ids;
                var i = 0;
                for (i; i < ids.length; i += 1) {
                    this.$el.find('#file-upload-delete' + guid + ids[i]).parent().remove();
                    this.root.sync_num -= 1;
                    this.root.selected_num -= 1;
                }

                this.dataReset();
                this.prompt(data.message || '上传失败', 'danger');
            },

            /**
             * 文件删除
             * @return {[type]} [description]
             */
            fileDelete: function($this, index){

                var $input;

                this.root.$fud = this.tpl(5)();
                var url = this.opt.del_url;

                // 删除状态
                var delStatus = bind(function (msg) {

                    if (msg.status == '0')
                        return this.prompt('文件删除失败', 'danger');

                    $input.remove();
                    $this.parent().remove();
                    this.root.sync_num -= 1;
                    if (this.root.sync_num <= 0)
                        this.root.$fuf.append(this.root.$fud);

                    if (this.isFunction(this.opt.delSuccess))
                        this.opt.delSuccess(index);

                    this.prompt('文件删除成功', 'success', 800);

                }, this)

                // 开始删除
                var del = bind(function (delSuc) {
                    var ele_id = $this.attr('id').replace('delete', 'id');
                    $input = $('#' + ele_id);
                    var path = $input.val();
                    $.post(url, { fp: path }, delStatus, 'json'); // toDo 更换 fp 参数名
                }, this)

                var fine_name = $this.siblings('span').html();

                if (this.isFunction(this.opt.delete)) {
                    this.opt.delete(fine_name, index, del);
                    return false;
                }

                if (confirm('确认删除 ' + fine_name + ' ?  ') == true) {
                    del();
                }
            },

            /**
             * 上传进度
             */
            fileSaveProgress: function(){

                var $files = this.root.$files;
                var args = arguments;
                var $file;

                var i;
                for (i in $files) {
                    $file = $files[i].find('.file-upload-filename');
                    $file[0].style.width = args[3] + " %";
                    $file.html(args[3] + '%');
                }
            },

            /**
             * 文件校验
             * @param  {obj} file
             * @return {}
             */
            fileVerify: function(file) {

                var img_type = ['jpg', 'jpeg', 'png', 'gif','webp'];
                var doc_type = ['doc', 'docx', 'txt', 'pdf'];
                var video_type = ['flv', 'mp4'];

                var postfix;

                if (typeof file === 'string') { // 对初始文件进行验证
                    postfix = file.substr(file.lastIndexOf('.') + 1).toLowerCase(); // 文件后缀
                    if (img_type.indexOf(postfix) > -1) {
                        return file.replace('b_', 't_b_'); // 获取缩略图
                    } else if (doc_type.indexOf(postfix) > -1) {
                        return '/img/word.jpg';
                    } else {
                        return '/img/video.png';
                    }
                } else {
                    if (file.size > this.opt.limit_size)
                        return this.prompt('未知错误 !!! ', 'danger');
                    postfix = file.name.substr(file.name.lastIndexOf('.') + 1).toLowerCase();
                };

                var all_type = img_type.concat(doc_type, video_type);

                // 生成预览
                if (all_type.indexOf(postfix) > -1) {
                    if (doc_type.indexOf(postfix) > -1)
                        file.url = '/img/word.jpg';
                    if (video_type.indexOf(postfix) > -1)
                        file.url = '/img/video.png';
                    return file;
                }

                return this.prompt('文件类型错误，请重新选择文件 !!!', 'danger');
            },

            /**
             * 获取文件详情
             * @param  {obj} file 文件
             * @return {obj}      文件详情
             */
            getFileInfo: function(file){
                var url  = window.URL.createObjectURL(file);
                var name = file.name;
                var size = file.size;
                var type = file.type;
                return {url:url, name:name, size:size, type:type};
            },

            /**
             * 数据重置
             */
            dataReset: function(){
                this.root.files  = [];
                this.root.$files = [];
                this.root.current_ids = [];
            },

            /**
             * 选取框重置
             */
            inputReset: function(){
                var $parent = this.root.$fileInput.parent();
                this.root.$fileInput = this.root.$fileInput.clone(true).val('');
                $parent.html( this.root.$fileInput[0] );
            },

            /**
             * 模版
             * @param  {int} type 模板
             * @return {dom}      dom
             */
            tpl: function(type){

                var fun, args, html, guid = this.root.GUID;

                return bind(function(){
                    args = arguments,
                    html = {

                        // 初始界面
                        0: '<div class="file-upload-dom" id="file-upload-dom'+guid+'">'
                                +'<div class="file-upload-prompt" id="file-upload-prompt'+guid+'">点击此框，或拖拽文件到此框中上传文件</div>'
                                +'<span class="file-upload-prompt2" id="file-upload-prompt2'+guid+'"></span>'
                                +'<span><input class="file-upload-input" id="file-upload-input'+guid+'" type="file" name="file[]" multiple /></span>'
                                +'<div class="file-upload-show" id="file-upload-show'+guid+'" > </div>'
                                +'<div  id="file-upload-files'+guid+'" ></div>'
                            +'</div>',

                        // 图片预览
                        1: '<span class="file-upload-show-item">'
                                +'<span class="file-upload-delete" id="file-upload-delete'+guid+args[1]+'">X</span>'
                                +'<img src="'+args[0]+'" alt="" />'
                                +'<span class="file-upload-filename">'+args[2]+'</span>'
                            +'</span>',

                        // 已成功上传图片
                        2: '<input id="file-upload-id'+guid+args[0]+'" type="hidden" name = "'+args[1]+'" value="'+ args[2] +'"/>',

                        //
                        3: '<div class="help-block text-left" >'+args[0]+'</div>',

                        // form 表单
                        4: '<form action="'+this.opt.url+'" method="post" enctype="multipart/form-data" style="display:none"></form>',

                        5: '<input id="file-upload-default'+guid+'" type="hidden" name = "'+this.opt.name+'" value=""/>'

                    };
                    return $(html[type]);
                },this);

            },

            /**
             * 默认提示
             * @param  {init} prompt 类型
             * @param  {int} color   颜色
             * @return {[arr]}
             */
            prompt: function(msg, color, time){

                var colors = {
                    'success': '#5cb85c',
                    'danger': '#d9534f',
                    'info': '#38f'
                }

                var time = time || 2000;

                if (this.isFunction(this.opt.alert))
                    return this.opt.alert( msg, color, time );

                this.root.$fup2.html(msg)
                    .css( {'background-color': colors[color]} )
                    .slideDown(500,function(){
                        $this = $(this);
                        setTimeout(function(){
                            $this.slideUp(500);
                        },time);
                    });

            },

            /**
             * 元素属否渲染完成
             * @param  {jquery}     $ele        jquery 对象
             * @param  {callback}   renderDone  渲染完成回调
             * @param  {intval}     timeout     超时时间 秒
             * @param  {callback}   loadTimeout 加载超时回调
             * @return
             */
            isEleRenderDone: function($ele, renderDone, timeout, loadTimeout){
                var _this = this;
                var num = 5 * (timeout || 3);
                (function() {
                    if ($ele.width() > 0 || $ele.is(':visible')) {
                        if (_this.isFunction(renderDone))
                            renderDone();
                        return false;
                    }
                    if (num <= 0) {
                        if (_this.isFunction(loadTimeout))
                            loadTimeout();
                        return false;
                    }
                    setTimeout(arguments.callee, 200);
                    num--;
                })();
            },

            isFunction: function  (param){
                return Object.prototype.toString.apply(param) == '[object Function]';
            },

            isString: function  (param){
                return Object.prototype.toString.apply(param) == '[object String]';
            }

        }
    }


    /**
     * 绑定函数this上下文
     * @param  {[type]} func    要绑定的函数
     * @param  {[type]} context this
     * @return {[type]}         绑定后的函数
     */
    bind = function(fn, context){
        return function(){
            return fn.apply(context, arguments);
        }
    }

    // 多个函数绑定
    bindAll = function(obj) {
        var i, key, length = arguments.length;
        if (length <= 1) throw new Error('_bindAll must be passed function names');
        for (i = 1; i < length; i++) {
            key = arguments[i];
            obj[key] = bind(obj[key], obj);
        }
        return obj;
    };


    /**
     * GUID
     * @param {int} len 
     */
    GUIDx4 = function(len){

        var len  = len || 1,
            guid = '',
            i;

        for( i=0; i<len; i+=1 )
            guid += (((1+Math.random())*0x10000)|0)
                    .toString(16)
                    .substring(1);

       return guid;
    }

    /**
     * GUID 固定长度
     */
    GUID = function(){
        var i, n, guid = "";
        for (i = 1; i <= 32; i++){
            n = Math.floor(Math.random()*16.0).toString(16);
            guid +=   n;
            if((i==8)||(i==12)||(i==16)||(i==20))
                guid += "-";
        }
        return guid;
    }

}));
