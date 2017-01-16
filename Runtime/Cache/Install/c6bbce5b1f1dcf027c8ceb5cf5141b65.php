<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta charset="utf-8">
<title>Hive</title>
<link href="/Public/Install/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-inverse">
  <div class="container">
    <div class="navbar-header"> <a class="navbar-brand" href="#">Infector</a> </div>
  </div>
</nav>
<div class="container"> 
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Pro</a></li>
    <li role="presentation"><a href="#kit" aria-controls="kit" role="tab" data-toggle="tab">Doc</a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home" style="padding-top:10px">
      <form method="POST" action="<?php echo U('infect');?>" class="form-horizontal">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <?php if(is_array($tables)): $i = 0; $__LIST__ = $tables;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;?><div class="panel panel-default">
              <div class="panel-heading" role="tab" id="heading<?php echo ($i); ?>">
                <h4 class="panel-title">
                  <label>
                    <input type="checkbox" class="sources" data-index="<?php echo ($i); ?>" value="<?php echo ($t["tn"]); ?>">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo ($i); ?>" aria-expanded="<?php if(($i) == "1"): ?>true<?php else: ?>false<?php endif; ?>" aria-controls="collapse<?php echo ($i); ?>"><?php echo ($t["tn"]); ?></a> </label>
                </h4>
              </div>
              <div id="collapse<?php echo ($i); ?>" class="panel-collapse collapse <?php if(($i) == "1"): ?>in<?php endif; ?>" role="tabpanel" aria-labelledby="heading<?php echo ($i); ?>">
                <div class="panel-body">
                  <ul class="list-group">
                    <li class="list-group-item">
                      <label><input type="checkbox" class="sources<?php echo ($i); ?>"> 默认启用数据</label>
                      <label><input type="checkbox" class="sources<?php echo ($i); ?>export"> 启用导出</label>
                      <label><input type="checkbox" class="sources<?php echo ($i); ?>sort"> 启用排序</label>
                      <label><input type="checkbox" class="sources<?php echo ($i); ?>frontModel"> 生成前端模型</label>
                    </li>
                  </ul>
                  <ul class="list-group">
                    <?php if(is_array($t["tc"])): $j = 0; $__LIST__ = $t["tc"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tcn): $mod = ($j % 2 );++$j;?><li class="list-group-item field<?php echo ($i); ?>"><?php echo ($tcn["Field"]); ?> 【<?php echo ($tcn["Type"]); ?>】
                        <label><input type="checkbox" class="null"> 判空</label>
                        <input type="hidden" class="key" value="<?php if(($tcn["Key"]) == "PRI"): ?>1<?php else: ?>0<?php endif; ?>">
                        <input type="hidden" class="field" value="<?php echo ($tcn["Field"]); ?>">
                        <input type="hidden" class="extra" value="<?php echo ($tcn["Comment"]); ?>">
                      </li><?php endforeach; endif; else: echo "" ;endif; ?>
                  </ul>
                </div>
              </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <input type="hidden" name="data">
        <button type="button" id="submit" class="btn btn-lg btn-danger">感染</button>
      </form>
    </div>
    <div role="tabpanel" class="tab-pane" id="kit">
      <h3>使用说明 <small>最后修改时间：2016-4-27</small></h3>
      <ul>
        <li>先导入基本表，注意修改表前缀</li>
        <li>修改common下的conf文件</li>
        <li>创建新的实体表
          <ul>
            <li>注意表的引擎（推荐MyISAM）</li>
            <li>注意表的主键（推荐全小写）</li>
            <li>字段必须备注，且按照以下格式（编辑类型|字段中文）</li>
            <li>编辑类型必须用大写</li>
            <li>编辑类型
              <ul>
                <li>UPLOADPIC 图片上传</li>
                <li>RICHTEXT 富文本</li>
                <li>TEXT 普通文本域</li>
                <li>TIMEPICKER 时间选择器</li>
                <li>SELECT 下拉框，需要自己填option</li>
                <li>不填 普通输入框</li>
                <li class="text-info">示例：UPLOADPIC|新闻封面</li>
              </ul>
            </li>
          </ul>
        </li>
        <li>勾选要生成的表</li>
        <li>生成后，根据实际逻辑修改对应文件</li>
        <li>项目正式上线后，必须删除install模块的所有内容</li>
      </ul>
      <h3>更新日志</h3>
      <ul>
        <li>16-5-30 修复后台可能的注入点，默认使用pdo引擎</li>
      </ul>
    </div>
  </div>
</div>
<script src="/Public/Install/js/jquery-2.0.3.min.js"></script> 
<script src="/Public/Install/js/bootstrap.min.js"></script> 
<script>
$(function() {
    var data = [], btn;
    function infect() {
        if (1 > data.length) {
            return btn.removeAttr("disabled"), alert("同化完成");
        }
        var current = JSON.stringify(data.pop());
        return $.ajax({
            type:"POST",
            url:'<?php echo U("infect");?>',
            data:{
                data:current
            },
            dataType:"JSON",
            success:function() {
                if (0 < data.length) {
                    return infect();
                }
                return btn.removeAttr("disabled"), alert("同化完成");
            }
        });
    }
    $("#submit").click(function() {
        btn = $(this);
        $(".sources").each(function() {
            var e = $(this);
            if (e.is(":checked")) {
                var idx = e.attr("data-index"), element = {
                    table:e.val(),
                    autoEnable:$(".sources" + idx).is(":checked") ? 1 :0,
                    "export":$(".sources" + idx + "export").is(":checked") ? 1 :0,
                    "sort":$(".sources" + idx + "sort").is(":checked") ? 1 :0,
                    "FM":$(".sources" + idx + "frontModel").is(":checked") ? 1 :0,
                    field:[]
                };
                $(".field" + idx).each(function() {
                    var f = $(this);
                    element.field.push({
                        key:f.find(".key").val(),
                        field:f.find(".field").val(),
                        "null":f.find(".null").is(":checked") ? 1 :0,
                        extra:f.find(".extra").val()
                    });
                });
                data.push(element);
            }
        });
        if (1 > data.length) {
            return alert("没有可供操作的数据");
        }
        return btn.attr("disabled", "disabled"), infect(btn);
    });
});
</script>
</body>
</html>