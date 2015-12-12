$(function() {
	//实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
    var ue = UE.getEditor('editor', {
        initialFrameWidth: 700,
        initialFrameHeight: 360
    }); 

    ue.ready(function() {
    	var $form = $("form"),
			$title = $form.find("[name=title]"),
			$fromplace = $form.find("[name=fromplace]"),
			$token = $form.find("[name=token]");

		$("#submit").click(function() {
			$.ajax({
				url: $form.attr("action"),
				type: "POST",
				dataType: "json",
	            data: {
	            	title: $title.val().trim(),
	            	fromplace: $fromplace.val().trim(),
	            	token: $token.val().trim(),
	            	newscontent: ue.getContent()
	            }
			}).done(function(data) {
				if(data.code == "0") {
					alert("上传成功！");
					$title.val("");
					$fromplace.val("");
					ue.setContent("");
				} else {
					alert("上传失败！\n"+data.msg);
				}
			});
			return false;
		});
    });
});