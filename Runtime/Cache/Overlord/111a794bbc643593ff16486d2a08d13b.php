<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>叹息之墙</title>
<link href="/Public/Overlord/css/login.css" media="all" rel="stylesheet" type="text/css">
<link href="/Public/Overlord/css/default_color.css" media="all" rel="stylesheet" type="text/css">
</head>

<body id="login-page">
<img src="/Public/Overlord/images/wallpaper.jpg" class="wallpaper">
<div id="main-content"> 
  <!-- 主体 -->
  <div class="login-body">
    <div class="login-main pr">
      <form action="<?php echo U('login');?>" class="login-form" method="post">
        <h3 class="welcome"> <i class="login-logo" style="background-size:cover;width:44px"></i> 叹息之墙 </h3>
        <div class="item-box" id="itemBox">
          <div class="item"> <i class="icon-login-user"> </i>
            <input autocomplete="off" name="username" placeholder="请填写用户名" type="text"/>
          </div>
          <span class="placeholder_copy placeholder_un"> 请填写用户名 </span>
          <div class="item b0"> <i class="icon-login-pwd"> </i>
            <input autocomplete="off" name="password" placeholder="请填写密码" type="password"/>
          </div>
          <span class="placeholder_copy placeholder_pwd"> 请填写密码 </span>
          <div class="item verifycode"> <i class="icon-login-verifycode"> </i>
            <input autocomplete="off" name="verify" placeholder="请填写验证码" type="text">
            <a class="reloadverify" href="javascript:void(0)" title="换一张"> 换一张？ </a>
            </input>
          </div>
          <span class="placeholder_copy placeholder_check"> 请填写验证码 </span>
          <div> <img alt="点击切换" class="verifyimg reloadverify" src="<?php echo U('Public/verify');?>"> </img> </div>
        </div>
        <div class="login_btn_panel">
          <button class="login-btn" type="submit"> <span class="in"> <i class="icon-loading"> </i> 登 录 中 ... </span> <span class="on"> 登 录 </span> </button>
          <div class="check-tips"> </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="http://static.ilongyuan.cn/cdn/script/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script type="text/javascript">
/* 登陆表单获取焦点变色 */
$(".login-form").on("focus", "input", function() {
    $(this).closest(".item").addClass("focus");
}).on("blur", "input", function() {
    $(this).closest(".item").removeClass("focus");
});

//表单提交
$(document).ajaxStart(function() {
    $("button:submit").addClass("log-in").attr("disabled", true);
}).ajaxStop(function() {
    $("button:submit").removeClass("log-in").attr("disabled", false);
});

$("form").submit(function() {
    var self = $(this);
    $.post(self.attr("action"), self.serialize(), success, "json");
    return false;
    function success(data) {
        if (data.status) {
            window.location.href = data.url;
        } else {
            self.find(".check-tips").text(data.info);
            //刷新验证码
            $(".reloadverify").click();
        }
    }
});

$(function() {
    //初始化选中用户名输入框
    $("#itemBox").find("input[name=username]").focus();
    //刷新验证码
    var verifyimg = $(".verifyimg").attr("src");
    $(".reloadverify").click(function() {
        if (verifyimg.indexOf("?") > 0) {
            $(".verifyimg").attr("src", verifyimg + "&random=" + Math.random());
        } else {
            $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/, "") + "?" + Math.random());
        }
    });
    //placeholder兼容性
    //如果支持 
    function isPlaceholer() {
        var input = document.createElement("input");
        return "placeholder" in input;
    }
    //如果不支持
    if (!isPlaceholer()) {
        $(".placeholder_copy").css({
            display:"block"
        });
        $("#itemBox input").keydown(function() {
            $(this).parents(".item").next(".placeholder_copy").css({
                display:"none"
            });
        });
        $("#itemBox input").blur(function() {
            if ($(this).val() == "") {
                $(this).parents(".item").next(".placeholder_copy").css({
                    display:"block"
                });
            }
        });
    }
});
</script>
</body>
</html>