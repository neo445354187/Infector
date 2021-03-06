<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo ($meta_title); ?></title>
<link href="/Public/favicon.ico" rel="shortcut icon" type="image/x-icon">
<link href="/Public/Overlord/css/base.css" media="all" rel="stylesheet" type="text/css">
<link href="/Public/Overlord/css/common.css" media="all" rel="stylesheet" type="text/css">
<link href="/Public/Overlord/css/module.css" rel="stylesheet" type="text/css">
<link href="/Public/Overlord/css/style.css" media="all" rel="stylesheet" type="text/css">
<link href="/Public/Overlord/css/default_color.css" media="all" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://static.ilongyuan.cn/cdn/script/jquery-1.8.3.min.js"></script>
<!--[if gte IE 9]><!-->
<script src="/Public/Overlord/js/jquery.mousewheel.js" type="text/javascript"></script>
<!--<![endif]-->

</head>

<body>
<!-- 头部 -->
<div class="header"> 
  <!-- Logo --> 
  <span class="logo" style="background:url(/Public/Overlord/images/logo.png) no-repeat center"> </span> 
  <!-- /Logo --> 
  <!-- 主导航 -->
  <ul class="main-nav">
    <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"> <a href="<?php echo (u($menu["url"])); ?>"> <?php echo ($menu["title"]); ?> </a> </li><?php endforeach; endif; else: echo "" ;endif; ?>
  </ul>
  <!-- /主导航 --> 
  <!-- 用户栏 -->
  <div class="user-bar"> <a class="user-entrance" href="javascript:;"> <i class="icon-user"> </i> </a>
    <ul class="nav-list user-menu hidden">
      <li class="manager"> 你好， <em title="<?php echo session('user_auth.username');?>"> <?php echo session('user_auth.username');?> </em> </li>
      <li> <a href="<?php echo U('User/updatePassword');?>"> 修改密码 </a> </li>
      <li> <a href="<?php echo U('User/updateNickname');?>"> 修改昵称 </a> </li>
      <li> <a href="<?php echo U('Public/logout');?>"> 退出 </a> </li>
    </ul>
  </div>
</div>
<!-- /头部 --> 
<!-- 边栏 -->
<div class="sidebar"> 
  <!-- 子导航 -->
  
    <div class="subnav" id="subnav">
      <?php if(!empty($_extra_menu)): ?>
        <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
      <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
        <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3> <i class="icon icon-unfold"> </i> <?php echo ($key); ?> </h3><?php endif; ?>
          <ul class="side-sub-menu">
            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li> <a class="item" href="<?php echo (u($menu["url"])); ?>"> <?php echo ($menu["title"]); ?> </a> </li><?php endforeach; endif; else: echo "" ;endif; ?>
          </ul><?php endif; ?>
        <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
  
  <!-- /子导航 --> 
</div>
<!-- /边栏 --> 
<!-- 内容区 -->
<div id="main-content">
  <div class="fixed alert alert-error" id="top-alert" style="display: none;">
    <button class="close fixed" style="margin-top: 4px;"> × </button>
    <div class="alert-content">  </div>
  </div>
  <div class="main" id="main">
     
      <!-- nav -->
      <?php if(!empty($_show_nav)): ?><div class="breadcrumb"> <span> 您的位置: </span>
          <assign name="i" value="1"> </assign>
          <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span> <?php echo ($v); ?> </span>
              <else> </else>
              <span> <a href="<?php echo ($k); ?>"> <?php echo ($v); ?> </a> > </span><?php endif; ?>
            <assign name="i" value="$i+1"> </assign><?php endforeach; endif; ?>
        </div><?php endif; ?>
      <!-- nav --> 
    
    
    <script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
    <form action="/index.php?s=/Overlord/Overall/index.html" method="post" class="form-horizontal">
        <?php if(!empty($data)): if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; switch($vo["type"]): case "1": ?><div class="form-item">
                            <label class="item-label"><?php echo ($vo["title"]); ?></label>
                            <div class="controls">
                                <input type="file" id="<?php echo ($vo["mark"]); ?>">
                                <input type="hidden" name="<?php echo ($vo["mark"]); ?>" id="<?php echo ($vo["mark"]); ?>_val" value="<?php echo ($vo["content"]); ?>"/>
                                <div class="upload-img-box">
                                    <div class="upload-pre-item"><img src="<?php echo ($vo["content"]); ?>"/></div>
                                </div>
                                <?php echo hook('QuickUpld', ['id' => $vo['mark'], 'return' => $vo['mark'].'_val', 'type' => 'path']);?> </div>
                        </div><?php break;?>
                    <?php case "2": ?><div class="form-item">
                            <label class="item-label"><?php echo ($vo["title"]); ?></label>
                            <div class="controls">
                                <label class="textarea">
                                    <textarea name="<?php echo ($vo["mark"]); ?>"><?php echo ($vo["content"]); ?></textarea>
                                </label>
                            </div>
                        </div><?php break;?>
                    <?php default: ?>
                    <div class="form-item">
                        <label class="item-label"><?php echo ($vo["title"]); ?></label>
                        <div class="controls">
                            <input type="text" class="text input-large" name="<?php echo ($vo["mark"]); ?>" value="<?php echo ($vo["content"]); ?>">
                        </div>
                    </div><?php endswitch; endforeach; endif; else: echo "" ;endif; endif; ?>
        <div class="form-item cf">
            <button class="btn submit-btn ajax-post" type="submit" target-form="form-horizontal">确 定</button>
        </div>
    </form>

  </div>
  <div class="cont-ft">
    <div class="copyright">
      <div class="fl"> </div>
      <div class="fr"> </div>
    </div>
  </div>
</div>
<!-- /内容区 --> 
<script type="text/javascript">
(function(){
  var ThinkPHP = window.Think = {
    "ROOT"   : "", //当前网站地址
    "APP"    : "/index.php?s=", //当前项目地址
    "PUBLIC" : "/Public", //项目公共目录地址
    "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
    "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
    "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
  }
})();
</script> 
<script src="/Public/static/think.js" type="text/javascript"></script> 
<script src="/Public/Overlord/js/common.js" type="text/javascript"></script> 
<script type="text/javascript">
+function() {
    var $window = $(window), $subnav = $("#subnav"), url;
    $window.resize(function() {
        $("#main").css("min-height", $window.height() - 130);
    }).resize();
    /* 左边菜单高亮 */
    url = window.location.pathname + window.location.search;
    url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
    $subnav.find("a[href='" + url + "']").parent().addClass("current");
    /* 左边菜单显示收起 */
    $("#subnav").on("click", "h3", function() {
        var $this = $(this);
        $this.find(".icon").toggleClass("icon-fold");
        $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").prev("h3").find("i").addClass("icon-fold").end().end().hide();
    });
    $("#subnav h3 a").click(function(e) {
        e.stopPropagation();
    });
    /* 头部管理员菜单 */
    $(".user-bar").mouseenter(function() {
        var userMenu = $(this).children(".user-menu ");
        userMenu.removeClass("hidden");
        clearTimeout(userMenu.data("timeout"));
    }).mouseleave(function() {
        var userMenu = $(this).children(".user-menu");
        userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
        userMenu.data("timeout", setTimeout(function() {
            userMenu.addClass("hidden");
        }, 100));
    });
    /* 表单获取焦点变色 */
    $("form").on("focus", "input", function() {
        $(this).addClass("focus");
    }).on("blur", "input", function() {
        $(this).removeClass("focus");
    });
    $("form").on("focus", "textarea", function() {
        $(this).closest("label").addClass("focus");
    }).on("blur", "textarea", function() {
        $(this).closest("label").removeClass("focus");
    });
    // 导航栏超出窗口高度后的模拟滚动条
    var sHeight = $(".sidebar").height();
    var subHeight = $(".subnav").height();
    var diff = subHeight - sHeight;
    //250
    var sub = $(".subnav");
    if (diff > 0) {
        $(window).mousewheel(function(event, delta) {
            if (delta > 0) {
                if (parseInt(sub.css("marginTop")) > -10) {
                    sub.css("marginTop", "0px");
                } else {
                    sub.css("marginTop", "+=" + 10);
                }
            } else {
                if (parseInt(sub.css("marginTop")) < "-" + (diff - 10)) {
                    sub.css("marginTop", "-" + (diff - 10));
                } else {
                    sub.css("marginTop", "-=" + 10);
                }
            }
        });
    }
}();
</script>

<script type="data/javascript">
//导航高亮
highlight_subnav('<?php echo U("Overall/index");?>');
</script>

</body>
</html>