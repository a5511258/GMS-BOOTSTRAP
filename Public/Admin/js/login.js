/**
 * Created by li on 16/12/30.
 */
$(document).ready(function () {

    <!-- 添加，密码输入回车后自动登录-->
    $(document).keypress(function (event) {
        var key = event.which;
        if (key == 13) {
            $("[id$=loginButton]").click(); //支持firefox,IE武校
            //$('input:last').focus();
            $("[id$=loginButton]").focus();  //支持IE，firefox无效。
            //以上两句实现既支持IE也支持 firefox
        }
    });


    $('#loginButton').click(function (e) {



    })


});