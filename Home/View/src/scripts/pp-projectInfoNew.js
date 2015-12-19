$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active");

	// 项目类型
	$("input[name=project_type], input[name=build_state]").siblings("span").click(function() {

		if($(this).hasClass("active")) {
			return;
		}

		$(this).addClass("active").siblings().removeClass("active");
		$(this).siblings("input").val($(this).data("filter"));
		
		$("#infoForm").attr("class", [
			["housetop", "ground", "ground"][$("input[name=project_type]").val()-1],
			["nonBuild", "build"][$("input[name=build_state]").val()-1]
		].join("_"));
	});

	// 省市区级联
	require("common/erqi/AreaData");
	require("common/erqi/cascadeSelect");
	$(".detail.part1 .area select").cascadeSelect(AreaData);

	require("common/erqi/customUpload");
	require("lib/jquery.form");
	
	// 上传图片
	$(".detail.part1 input[type=file]").customUpload({
		bg_url: "upload.png",
		uploadType: "image",
		width: "120px",
		height: "120px",
		callback: function(type) { // 添加或删除图片
			// 显示或清除图片名称
			var $prename = $(this).parent().siblings(".previewname");
			if(type === "add") {
				$prename.text(this.files[0].name);
			} else {
				$prename.text("");
			}

			// 增加或移除图片上传框，并更新索引
			if(type === "add") {
				
			} else {
				
			}
		}
	});

	// 上传文件
	$(".detail.part2 input[type=file]").customUpload({
		img_url: "attachment.png",
		content: "上传附件",
		uploadType: "file",
		width: "80px",
		height: "20px"
	});

	// 有无（附件）
	$("select").filter(function(){
		return $(this).data("withFile");
	}).change(function(e) {
		var $inputWrap = $(this).siblings(".input-wrap"),
			$preview = $(this).siblings(".preview");
		if(this.value === "1") { // 有
			$inputWrap.show();
		} else { // 无
			$inputWrap.hide().find("input").val("");
			$preview.hide().find("a").attr("href", "javascript:;").text("");
		}
	});

	// 其他（可填写）
	$("select").filter(function(){
		return $(this).data("withOther");
	}).change(function(e) {
		var value = this.value;
		if(value === "0") { // 其他
			$(this).siblings(".other").show();
		} else {
			$(this).siblings(".other").hide().val("");
		}
	});

	// 日期选择框
	require("lib/jquery-ui.min");
	$("input[data-type=date]").datepicker();

	// 保存资料
	var options = {
	   	// target: '#output',          //把服务器返回的内容放入id为output的元素中      
	   	beforeSubmit: beforeSubmit, //提交前的回调函数  
	   	success: successCallback,  	//提交后的回调函数
	   	dataType: "json",           //html(默认), xml, script, json...接受服务端返回的类型  
	   	// clearForm: true,         //成功提交后，清除所有表单元素的值  
	   	// resetForm: true,         //成功提交后，重置所有表单元素的值  
	   	timeout: 6000               //限制请求的时间，当请求大于3秒后，跳出请求
	};
	  
	function beforeSubmit(formData, jqForm, options){

		if($("#submit").hasClass("disabled")) {
			return false;
		}

	   	$("#submit").addClass("disabled");

	   	return true;
	}

	function successCallback(data) {
		if(data.code == "0") {
			$("#submit").removeClass("disabled");
			alert("上传成功！");
		} else {
			alert("上传失败！\n"+data.errmsg);
		}
	}

	$form = $("#infoForm");
	$form.find("input[type=submit]").click(function() {
		var optype = $(this).data("optype");
		if(optype === "delete") {
			$.ajax({
				type: $form.attr("method"),
				url: $form.attr("action"),
				data: {
					optype: optype,
					project_code: $form.find("[name=project_code]").val()
				}
			}).done(function(data) {
				if(data.code == "0") {
					alert("删除成功！");
					location.href = "?c=ProjectProviderMyPro&a=awaitingAssessment";
				} else {
					alert("删除失败！");
				}
			}).fail(function() {
				alert("删除失败！");
			});
			return false;
		} else {
			$form.find("[name=optype]").val(optype);
			return true;
		}
	});

	$form.ajaxForm(options);

});