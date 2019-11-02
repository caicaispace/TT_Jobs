// checkh html5
(function () {

    // var $height = $(window).height();
    // $(".loginBack").css("height",$height);
    // $(window).resize(function(){
    //  $(".loginBack").css("height",$(this).height());
    // })


    // if (typeof(Worker) !== "undefined") {

    // } else {
    //  $('.login').remove();
    //  $('.browser_download').show();
    // }

})();

$(function () {
    $('#form').submit(function () {

        var username = $("input[name='username']").val();
        var mobile = $("input[name='mobile']").val();
        var password = $("input[name='password']").val();
        var code = $("input[name='code']:visible").val();

        code = code === '' ? 1 : code;
        $('#msg').empty();
        $('#msg').fadeIn();

        var pathname = '';

        // if (pathname === '/admin') {
        //     if (username.length > 20) {
        //         $('#msg').html('<h4 style="color:#ff4400;">用户名过长</h4>');
        //         return;
        //     }
        // }
        // if (pathname === '/business') {
        //
        // }
        var post_url = pathname + '/login';
        var post_data = {
            'username': username
            , 'mobile': mobile
            , 'password': password
            , 'code': code
        };

        $.post(
            post_url,
            post_data,
            function (response) {
                if (response.status === 1) {
                    location.reload();
                } else {
                    var message, code;
                    if (typeof response.message === 'object') {
                        for (var i in response.message) {
                            message = response.message['msg'];
                            code = response.message['code'];
                        }
                    } else {
                        message = response.message;
                        code = 0;
                    }
                    if (code === '1') {
                        $('#code').show(300);
                    }
                    if (message === '') {
                        message = '系统未知错误！';
                    }
                    $('#msg').html('<h4 style="color:#ff4400;">' + message + '</h4>')
                    var timer = setTimeout(function () {
                        $('#msg').fadeOut(500);
                        clearTimeout(timer);
                    }, 3000)
                }
            }
        );
        return false;
    });
    // $("input[name='code']").focus(function (){
    //  $(this).keyup(function (){
    //      var val = $(this).val();
    //      if(val.length>4){
    //          $('.msgCode').css('display','inline-block');
    //          $(this).val(val.substr(0,5));
    //          return false;
    //      }else{
    //          $('.msgCode').css('display','none');
    //      }
    //  })
    // })
    // $('.code').click(function (){
    //  var src = $(this).attr('src').split('?')[0];
    //  $(this).attr('src',src);
    //  var url = $(this).attr('src')+'?'+Math.random();
    //  $(this).attr('src',url);
    // })
});

// $(function () {
//     $('#form').submit(function () {
//
//         var username = $("input[name='username']").val();
//         var mobile = $("input[name='mobile']").val();
//         var password = $("input[name='password']").val();
//         var code = $("input[name='code']:visible").val();
//
//         code = code === '' ? 1 : code;
//         $('#msg').empty();
//         $('#msg').fadeIn();
//
//         var host = location.origin;
//
//         var post_url = host + '/login';
//         var post_data = {
//             'username': username
//             , 'mobile': mobile
//             , 'password': password
//             , 'code': code
//         };
//
//         $.post(
//             post_url,
//             post_data,
//             function (response) {
//                 if (response.status === 1) {
//                     location.reload();
//                 } else {
//                     var message, code;
//                     if (typeof response.message === 'object') {
//                         for (var i in response.message) {
//                             message = response.message['msg'];
//                             code = response.message['code'];
//                         }
//                     } else {
//                         message = response.message;
//                         code = 0;
//                     }
//                     if (code === '1') {
//                         $('#code').show(300);
//                     }
//                     if (message === '') {
//                         message = '系统未知错误！';
//                     }
//                     $('#msg').html('<h4 style="color:#ff4400;">' + message + '</h4>')
//                     var timer = setTimeout(function () {
//                         $('#msg').fadeOut(500);
//                         clearTimeout(timer);
//                     }, 3000)
//                 }
//             }
//         );
//         return false;
//     });
// });