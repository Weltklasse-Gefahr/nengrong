$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active");

	// 项目类型
	$("input[name=project_type], input[name=build_state]").siblings("span").click(function() {

		if($(this).hasClass("active")) {
			return;
		}

		$(this).addClass("active").siblings().removeClass("active");
		$(this).siblings("input").val($(this).data("filter"));
		$form.find("li:hidden input, li:hidden select").prop("disabled", false);

		$("#infoForm").attr("class", [
			["housetop", "ground", "bigground"][$("input[name=project_type]").val()-1],
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
	function uploadCallback(type) { // 添加或删除图片
		// 显示或清除图片名称
		var $prename = $(this).parent().siblings(".previewname");
		if(type === "add") {
			$prename.text(this.files[0].name).attr("title", this.files[0].name);
		} else {
			$prename.text("").attr("title", "");
		}

		var fileTpl = '<div class="img-ct">\
<input type="file" data-type="mul" accept="image/gif,image/jpeg,image/png" name="picture_mul" style="visibility: hidden;" />\
<p class="previewname"></p>\
</div>';

		// 增加或移除图片上传框，最多12张图片
		if(type === "add") {
			if(this.name === "picture_mul") {
				var count = $(this).parents(".img-ct").siblings(".img-ct").length;
				if(count < 11) {
					$(this).parents(".img-ct").after(fileTpl).next().find('input[type=file]').customUpload({
						bg_url: "upload.png",
						uploadType: "image",
						width: "120px",
						height: "120px",
						callback: uploadCallback
					});
				}
			}
		} else {
			if(this.name === "picture_mul") {
				var ct = $(this).parents(".item");
				$(this).parents(".img-ct").remove();
				var mul_items = ct.children(".img-ct").filter(function(){
					return !!$(this).find('[data-type=mul]').length;
				});
				if(mul_items.last().find('[data-type=mul]').val()) {
					mul_items.last().after(fileTpl).next().find('input[type=file]').customUpload({
						bg_url: "upload.png",
						uploadType: "image",
						width: "120px",
						height: "120px",
						callback: uploadCallback
					});
				}
			}
		}
	}
	$(".detail.part1 input[type=file]").customUpload({
		bg_url: "upload.png",
		uploadType: "image",
		width: "120px",
		height: "120px",
		callback: uploadCallback
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
	}).change();

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
	}).change();

	// 日期选择框
	require("lib/jquery-ui");
	$.datepicker.regional["zh-CN"] = { closeText: "关闭", prevText: "上月", nextText: "下月", currentText: "今天", monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"], monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"], dayNames: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"], dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"], dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"], weekHeader: "周", dateFormat: "yy-mm-dd", firstDay: 1, isRTL: !1, showMonthAfterYear: !0, yearSuffix: "年" };
	$.datepicker.setDefaults($.datepicker.regional['zh-CN']);
	$("input[data-type=date]").datepicker({
		changeMonth: true,
      	changeYear: true
	});

	// 组件、逆变器
	$(".component").on("click", ".add", function() {
		var $parent = $(this).parent();
		$parent.append($parent.hasClass("inverter") ? '<div class="item">\
<a href="javascript:;" class="del">删除</a>\
<div><span class="c0">逆变器厂家</span><input class="c0" name="inverter_company[]"/></div>\
<div><span class="c0">规格型号</span><input class="c0" name="inverter_type[]"/><span class="c1">数量</span><input class="c1" name="inverter_count[]"/>个</div>\
</div>' : '<div class="item">\
<a href="javascript:;" class="del">删除</a>\
<div><span class="c0">组件厂家</span><input class="c0" name="component_company[]"/></div>\
<div><span class="c0">规格型号</span><input class="c0" name="component_type[]"/><span class="c1">数量</span><input class="c1" name="component_count[]"/>个</div>\
</div>');
	}).on("click", ".del", function() {
		var $parent = $(this).parent(),
			items = $parent.siblings(".item");
		if(items.length) {
			$(this).parent().remove();
		} else {
			alert(($parent.parent().hasClass("inverter") ? "逆变器" : "组件" ) + "必须至少有一个");
		}
	});

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
	  
	function beforeSubmit(formData, jqForm, options) {

		if($("#submit").hasClass("disabled")) {
			return false;
		}

	   	$("#submit").addClass("disabled");

	   	return true;
	}

	function successCallback(data) {
		if(data.code == "0") {
			$("#submit").removeClass("disabled");
			location.href = "?c=ProjectProviderMyPro&a=projectInfoEdit&project_code=" + data.id;
		} else {
			$form.find('[data-type="mul"]').each(function() {
				$(this).attr("name", $(this).attr("name").replace(/^([^\[\]]*)\[\]$/, "$1"));
			});
			alert(data.errmsg || "操作失败！");
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
			$form.find("li:hidden input, li:hidden select").prop("disabled", true);
			$form.find('[data-type="mul"]').each(function() {
				$(this).attr("name", $(this).attr("name").replace(/^(.*)$/, "$1[]"));
			});
			return true;
		}
	});

	$form.ajaxForm(options);

});