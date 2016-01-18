$(function() {

	$(".l-nav").find(".intent").addClass("active")
		.children("a").attr("href", "javascript:;");

	//实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
    var ue = UE.getEditor('editor', {
    	toolbars: [['forecolor']],
    	serverUrl: '',
    	elementPathEnabled: false,
    	wordCount: false,
        initialFrameWidth: 670,
        initialFrameHeight: 340
    });
    window.ue = ue;

    require("common/erqi/dialog");
    var param = $.parseQueryParam();
    ue.ready(function() {
    	var $form = $("form");

    	function saveOrSubmitIntent(opt) {
    		opt = opt || {};
    		$.ajax({
				url: $form.attr("action"),
				type: "POST",
				dataType: "json",
	            data: {
	            	yixiangshu: ue.getContent(),
	            	optype: opt.optype,
	            	no: param.no,
	            	token: param.token
	            }
			}).done(function(data) {
				if(data.code == "0") {
					alert("操作成功！");
					if(opt.successCallback) {
						opt.successCallback();
					}
				} else {
					alert(data.msg ||　"操作失败！");
				}
			}).fail(function() {
				alert("操作失败！");
			});
    	}

		$("input[type=submit]").click(function(e) {

			var optype = $(this).data("optype");
			if(optype === "save") {
				saveOrSubmitIntent({
					optype: optype
				});
			} else {
				$.confirm("提交后无法修改，是否确认提交？").done(function() {
					saveOrSubmitIntent({
						optype: optype,
						successCallback: function() {
							ue.disable(true);
							$("input[type=submit]").addClass("disabled").prop("disabled", true);
							$(".signed").show();
						}
					});
				}).fail(function() {
				});
			}
			
			return false;
		});
    });
});