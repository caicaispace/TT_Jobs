define(['jquery.form.min'],function(form){


    /**
     * 绑定函数this上下文
     * @param  {[type]} func    要绑定的函数
     * @param  {[type]} context this
     * @return {[type]}         绑定后的函数
     */
    var bind = function(func, context) {
        var bound = function(){
            func.apply(context,arguments);
        };
        return bound;
    };

    /**
     * options 配置
     *
     * url    string     提交URL
     * fileSelect   string/jquery/ele  file控件，需要被独立的标签包裹
     * fileChange   func 文件改变回调 params(file_info) file_info {url:选择的图片,name:文件名}
     * start        func 开始上传文件 params(file_id)
     * success      func 文件上传成功回调 params(file_id,server_info)
     * error        func 文件上传失败 params(file_id,server_info)
     * batch=false  bool 是否批量上传，默认单独上传
     */
    var Upload = function(options){

        var params = {
            action  :options.url || "",
            method  :"post",
            enctype :"multipart/form-data",
            style   :"display:none"
        };
        this.form  = $('<form>',params);
        this.opt   = options || {};
        this.files = [];

        this._fileChange = bind(this._fileChange,this);
        this.file = $(options.fileSelect).change(this._fileChange);
    };

    /**
     * 移除已选择的文件
     * @param  {[int]} id 已选择的文件ID
     */
    Upload.prototype.remove = function(id){
        if (this.files[id]){
            delete this.files[id];
        }
        return true;
    };

    /**
     * 保存选择的文件
     */
    Upload.prototype.save = function(data){
        this._data = data;
        document.body.appendChild(this.form[0]);
        if (this.opt.batch){
            for(var i in this.files){
                this.files[i].el.appendTo(this.form);
            }
            this._batchSubmit();
        }else{
            this._oneSubmit();
        }

    };

    Upload.prototype._batchSubmit = function(){
        if (this.opt.start) this.opt.start();

        this.form.ajaxSubmit({
            data : this._data,
            dataType : 'json',
            success : this.opt.success,
            error   : this.opt.error
        });
    };

    Upload.prototype._oneSubmit = function(){
        this.form.html('');
        var file = this.files.shift();
        if (!file) return ;
        file.el.appendTo(this.form);
        if (this.opt.start) this.opt.start(file.id);

        var success = this.opt.success;
        var error   = this.opt.error;
        var options = {dataType:'json',data:this._data};

        options.success = bind(function(data){
            this._oneSubmit();
            if (data.status && success){
                success(data,file.id);
                return ;
            }
            if (!data.status && error){
                error(data,file.id);
            }
        },this);

        options.error = bind(function(info){
            this._oneSubmit();
            if (error) error(info,file.id);
        },this);

        this.form.ajaxSubmit(options);
    };

    Upload.prototype._fileChange = function(){
        var parent = this.file.parent();

        var id = this.files.push({el:this.file,id:this.files.length})-1;
        var info = this._getFileInfo(this.file[0]);
        info.id = id;
        if (this.opt.fileChange)
            this.opt.fileChange(info);

        this.file = this.file.clone(true).val('');
        parent.html(this.file[0]);
    };

    Upload.prototype._getFileInfo = function(file){
        var url,name,size,type;
        if (navigator.userAgent.indexOf("MSIE")>=1) { // IE
            url = file.value;
            name = this._getFileNameByPath(url);
        } else {
            url  = window.URL.createObjectURL(file.files.item(0));
            name = this._getFileNameByPath(file.value);
            size = file.files.item(0).size;
            type = file.files.item(0).type;
        }
        return {url:url,name:name,size:size,type:type};
    };

    Upload.prototype._getFileNameByPath = function(path){
        var pos1 = path.lastIndexOf('/');
        var pos2 = path.lastIndexOf('\\');
        var pos  = Math.max(pos1, pos2);
        if( pos<0 )
            return path;
        else
            return path.substring(pos+1);
    };

    return Upload;

});