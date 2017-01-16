<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
$("#<?php echo ($addons_data["id"]); ?>").uploadify({
    "height":30,
    "swf":"/Public/static/uploadify/uploadify.swf",
    "fileObjName":"download",
    "buttonText":"上传图片",
    "uploader":"<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
    "width":120,
    'removeTimeout':1,
    'fileTypeExts':'*.jpg; *.png; *.gif;',
    "onUploadSuccess":function(file, data){
        var data = $.parseJSON(data);
        var src = '';
        if(data.status){
            $("#<?php echo ($addons_data["return"]); ?>").val(data.<?php echo ($addons_data["type"]); ?>);
            src = data.url || data.path
            $("#<?php echo ($addons_data["return"]); ?>").parent().find('.upload-img-box').html(
                '<div class="upload-pre-item"><img src="' + src + '"/></div>'
            );
        } else {
            updateAlert(data.info);
            setTimeout(function(){
                $('#top-alert').find('button').click();
                $(that).removeClass('disabled').prop('disabled',false);
            },1500);
        }
    },
    "onFallback":function() {
        alert('未检测到兼容版本的Flash.');
    }
});
</script>