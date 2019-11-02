/**
 * webSocket
 */
define(function(){ return{

        init: function(options) {

            var _this    = this;
            var func     = function(){}
            var wsServer = options.wsServer || 'ws://192.168.16.32:1925'; // ip 端口

            this.getMsg = options.getMsg || func; // 接收消息回调
            this.open   = options.open || func; // 链接成功回调
            this.close  = options.close || func; // 链接关闭回调

            this.isConnect = false;
            this.ws           = new WebSocket(wsServer);
            this.ws.onopen    = function (evt) { _this._onOpen(evt) };
            this.ws.onclose   = function (evt) { _this._onClose(evt) };
            this.ws.onmessage = function (evt) { _this._onMessage(evt) };
            this.ws.onerror   = function (evt) { _this._onError(evt) };

        },

        // 消息发送
        sendMsg: function(msg) {
            if(this.isConnect){
                this.ws.send(msg);
            }else{
                console.log('链接失败！！！');
            }
        },

        // 链接打开
        _onOpen: function(evt) {
            console.log("连接服务器成功");
            this.isConnect = true;
            this.open();
        },

        // 链接关闭
        _onClose: function(evt) {
            console.log("Disconnected");
            this.close();
        },

        // 接收消息
        _onMessage: function(evt) {
            var data = JSON.parse(evt.data);
            this.getMsg(data);
        },

        // 错误输出
        _onError: function(evt) {
            console.log('Error occured: ' + evt.data);
        }

    }
});
