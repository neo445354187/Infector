/**
 * H5+ajax方式上传图片2016/10/11.
 */
+function($) {
    $.fn.upload = function(options) {
        var operation = {
            requesting:false,
            //请求路径
            url:"",
            //进行操作的元素，这里可以通过样式名 引入样式表中的样式。
            size:"2",
            //2Mb  上传图片大小 (单位：字节)  最大
            imgType:"image/png,image/gif,image/jpeg,image/jpg",
            //图片类型
            callback:function(json) {
                alert(json.info);
            }
        };
        var options = $.extend(operation, options);
        this.each(function() {
            //绑定事件
            $(this).change(function(event) {
                //判断图片类型  大小
                var s = $(this), files = event.target.files, file = files[0];
                if (!options.imgType.match(file.type)) {
                    alert("请上传" + options.imgType + "格式的图片！");
                    return false;
                }
                if (file.size > operation.size * 1024e3) {
                    alert("图片大小不能大于" + options.size + "M");
                    return false;
                }
                if (operation.requesting) {
                    return false;
                }
                var reader = new FileReader();
                reader.readAsDataURL(file);
                // 将文件以Data URL形式进行读入页面
                reader.onload = function() {
                    // 渲染文件+上传图片，上传成功后显示图片。
                    var fd = new FormData();
                    fd.append("uploadpicture", file), operation.requesting = true, s.text("上传中..."), $.ajax({
                        type:"POST",
                        url:operation.url,
                        data:fd,
                        dataType:"JSON",
                        cache:false,
                        processData:false,
                        contentType:false,
                        success:function(json) {
                            operation.requesting = false, s.text("上传文件"), options.callback.call(this, json);
                        }
                    });
                };
            }).hide();
        });
    };
}(jQuery);