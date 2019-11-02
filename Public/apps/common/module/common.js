define(['m/emptyModel', 'c/emptyCollection', 'notification'],
	function(model, collection, notification) {

		function error(mc, resp, options) {
			var err = resp.message || '未知错误';
    		notification({type:'error', msg: err});
			// var width = window.innerWidth;
			// if (width > 768) {
			// 	toastr["error"](resp.message);
			// }else{
			// 	toastr.options = {
			// 		"closeButton": true,
			// 		"positionClass": "toast-top-full-width"
			// 	}
			// 	toastr["error"](resp.message);
			// };
		}

        function success(msg) {
            var text = msg ? msg : '操作成功';
            notification('success', text);
        }

		function getModel(options) {
			var new_model = new model(options);
			new_model.on('error', error);
			return new_model;
		}

		function getCollection(options) {
			var new_collection = new collection(options);
			new_collection.models = [];
			new_collection.models[0] = getModel();
			new_collection.on('error', error);
			return new_collection;
		}

        function confirm (opt){
            if (!opt) opt = {};
            opt = $.extend(true,
            {
                title: 'title',
                content: 'content',
                button: ['取消','确认'],
                callback: ''

            },opt);
            var tpl = '<div class="modal modal-confirm" tabindex="-1" role="dialog" data-backdrop="static">'+
                       '<div class="modal-dialog modal-sm" role="document">'+
                           '<div class="modal-content">'+
                               '<div class="modal-body">'+
                                  '<br />'+
                                  '<center><h3 >'+opt.title+'</h3></center>'+
                                  '<center><span >'+opt.content+'</span></center>'+
                               '</div>'+
                               '<div class="modal-footer">'+
							   '<button data-confirm="cancel" type="button" class="btn btn-default" data-dismiss="modal">'+opt.button[0]+'</button>'+
							   '<button data-confirm="confirm" type="submit" class="btn btn-primary" data-price-count="express">'+opt.button[1]+'</button>'+
                             '</div>'+
                           '</div>'+
                       '</div>'+
                    '</div>';
            var $modal = $(tpl).modal({
                keyboard: false
            });
            $modal.find('[data-confirm]').on('click', function(that){
                $modal.modal('hide').remove();
                var state = $(this).attr('data-confirm') === 'confirm' ? true : false;
                if (typeof opt.callback === 'function'){
                    opt.callback(state)
                }
            })
        }

		/**
		 * notification
		 */
		function note(type, msg){
    		notification({type: type, msg: msg});
		}

		// Chrome notifications
		if (Notification && Notification.permission !== "granted") {
			Notification.requestPermission(function (status) {
				if (Notification.permission !== status) {
					Notification.permission = status;
				}
			});
		}

		/**
		 * global notification
		 */
		function gnote(title, msg) {
			title = title || '标题';
			msg = msg || '消息';
			var options = {
				dir: "rtl"
				, lang: "zh-CN"
				, body: msg
				// , icon: "http://i.stack.imgur.com/dmHl0.png"
				// , tag:"msgId"
			};
			new Notification(title, options);
		}

		function getConstants(model_name) {
            return APP.hashs.models.constants[model_name];
        }

        function getConstantsReflection(model_name) {
            return APP.hashs.models.constants_reflection[model_name];
        }

		return {
			'confirm': confirm
            , 'error': error
			, 'success': success
			, 'alert': alert
			, 'note': note
			, 'gnote': gnote
			, 'getModel': getModel
			, 'getCollection': getCollection
			, 'getConstants': getConstants
			, 'getConstantsReflection': getConstantsReflection
		}

	}
);
