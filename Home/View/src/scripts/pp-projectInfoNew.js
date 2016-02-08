$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active");

	// 项目类型
	$("input[name=project_type], input[name=build_state]").siblings("span").click(function() {

		if($(this).hasClass("active")) {
			return;
		}

		$(this).addClass("active").siblings().removeClass("active");
		$(this).siblings("input").val($(this).data("filter"));
		$form.find("li:hidden input[name], li:hidden select[name]").prop("disabled", false);

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
<input type="file" accept="image/*" data-type="mul" name="picture_mul[]" style="visibility: hidden;" />\
<p class="previewname"></p>\
</div>';

		// 增加或移除图片上传框，最多12张图片
		if(type === "add") {
			if(this.name === "picture_mul[]") {
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
			if(this.name === "picture_mul[]") {
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
		fileSizeLimit: {
			size: 10*1024*1024
		},
		callback: uploadCallback
	});

	// 上传文件
	$(".detail.part2 input[type=file]").customUpload({
		img_url: "attachment.png",
		content: "上传附件",
		uploadType: "file",
		width: "80px",
		height: "20px",
		fileSizeLimit: {
			size: 10*1024*1024
		}
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
<div><span class="c0">逆变器厂家</span><input class="c0" r-name="inverter_company" name="inverter_company[]"/></div>\
<div><span class="c0">规格型号</span><input class="c0" r-name="inverter_type" name="inverter_type[]"/><span class="c1">数量</span><input class="c1" r-name="inverter_count" name="inverter_count[]"/>个</div>\
</div>' : '<div class="item">\
<a href="javascript:;" class="del">删除</a>\
<div><span class="c0">组件厂家</span><input class="c0" r-name="component_company" name="component_company[]"/></div>\
<div><span class="c0">规格型号</span><input class="c0" r-name="component_type" name="component_type[]"/><span class="c1">数量</span><input class="c1" r-name="component_count" name="component_count[]"/>个</div>\
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
		data: {
			rtype: 1
		},
	   	// target: '#output',          //把服务器返回的内容放入id为output的元素中      
	   	beforeSubmit: beforeSubmit, //提交前的回调函数
	   	success: successCallback,  	//提交后的回调函数
	   	error: failCallback,		//服务器出错的回调函数
	   	dataType: "json",           //html(默认), xml, script, json...接受服务端返回的类型  
	   	// clearForm: true,         //成功提交后，清除所有表单元素的值  
	   	// resetForm: true,         //成功提交后，重置所有表单元素的值  
	   	timeout: 6000               //限制请求的时间，当请求大于3秒后，跳出请求
	};
	  
	$form = $("#infoForm");
	$form.validate({
		ignore: ':hidden, [data-with-file="true"]',
		rules: {
			"project_name": "required",
			"province": "required",
			"city": "required",
			"county": "required",
   			"project_address": "required",

   			"contacts_name": "required",
   			"contacts_phone": {
   				"required": true,
				"mobile": true

   			},
   			"contacts_email": {
   				"required": true,
   				"email": true
   			},

   			"housetop_owner": "required",
   			"company_capital": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
   			"electricity_total": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
   			"electricity_pay": "required",

   			"housetop_type_other": {
   				"required": function() {
   					if($('[name="housetop_type"]').is(":visible") && $('[name="housetop_type"]').val() == "0") {
   						return true;
   					}
   					return false;
	   			}
	   		},
   			"housetop_area": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
   			"housetop_waterproof_time": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
   			"housetop_age": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
   			"housetop_direction_other": {
   				"required": function() {
   					if($('[name="housetop_direction"]').is(":visible") && $('[name="housetop_direction"]').val() == "0") {
   						return true;
   					}
   					return false;
	   			}
	   		},
	   		"housetop_load": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},

	   		"ground_property_other":  {
   				"required": function() {
   					if($('[name="ground_property"]').is(":visible") && $('[name="ground_property"]').val() == "0") {
   						return true;
   					}
   					return false;
	   			}
	   		},
	   		"ground_area": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
	   		// "rent_time": "required",
	   		// "rent_pay": "required",
	   		"control_room_area": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
	   		"sell_sum": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},

	   		"transformer_capacity": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
	   		"voltage_level": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
	   		"electricity_distance": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},

	   		"company_invest": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
	   		"company_EPC": "required",
	   		"capacity_level": "required",

	   		"synchronize_date": {
	   			"required": true,
	   			"dateISO": true
	   		},
	   		"electricity_data": {
	   			"required": true,
	   			"number": true,
	   			"min": 0
	   		},
	   		"plan_financing": {
	   			"required": true,
	   			"number": true,
	   			"min": 0
	   		}
   		},
   		messages: {
   			"project_name": "请填写项目名称",
   			"province": "请选择省份",
   			"city": "请选择市",
			"county": "请选择区",
			"project_address": "请填写详细地址",

			"contacts_name": "请填写项目联系人",
			"contacts_phone": {
   				"required": "请填写联系方式",
				"mobile": "联系人手机号格式不对"
   			},
   			"contacts_email": {
   				"required": "请填写联系人邮件地址",
   				"email": "联系人邮箱地址格式不对"
   			},

			"housetop_owner": "请填写屋顶业主名称",
			"company_capital": {
				"required": "请填写注册资本金",
				"number": "注册资本金应为大于0的数字",
				"min": "注册资本金应为大于0的数字"
			},
   			"electricity_total": {
				"required": "请填写年用电量",
				"number": "年用电量应为大于0的数字",
				"min": "年用电量应为大于0的数字"
			},
   			"electricity_pay": {
				"required": "请填写电费",
				"number": "电费应为大于0的数字",
				"min": "电费应为大于0的数字"
			},

   			"housetop_type_other": "请填写屋顶类型",
   			"housetop_area": {
				"required": "请填写屋顶面积",
				"number": "屋顶面积应为大于0的数字",
				"min": "屋顶面积应为大于0的数字"
			},
   			"housetop_waterproof_time":  {
				"required": "请填写屋顶防水周期",
				"number": "屋顶防水周期应为大于0的数字",
				"min": "屋顶防水周期应为大于0的数字"
			},
   			"housetop_age": {
				"required": "请填写屋顶使用寿命",
				"number": "屋顶使用寿命应为大于0的数字",
				"min": "屋顶使用寿命应为大于0的数字"
			},
   			"housetop_direction_other": "请填写屋顶朝向",
   			"housetop_load": {
				"required": "请填写屋顶活载荷",
				"number": "屋顶活载荷应为大于0的数字",
				"min": "屋顶活载荷应为大于0的数字"
			},

   			"ground_property_other": "请填写土地性质",
   			"ground_area": {
				"required": "请填写租赁土地面积",
				"number": "租赁土地面积应为大于0的数字",
				"min": "租赁土地面积应为大于0的数字"
			},
   			// "rent_time": "请填写租赁年限",
   			// "rent_pay": "请填写租赁租金",
	   		"control_room_area": {
				"required": "请填写中控室建筑面积",
				"number": "中控室建筑面积应为大于0的数字",
				"min": "中控室建筑面积应为大于0的数字"
			},
	   		"sell_sum": {
				"required": "请填写出让金额",
				"number": "出让金额应为大于0的数字",
				"min": "出让金额应为大于0的数字"
			},

	   		"transformer_capacity": {
				"required": "请填写上级变压器容量",
				"number": "上级变压器容量应为大于0的数字",
				"min": "上级变压器容量应为大于0的数字"
			},
	   		"voltage_level": {
				"required": "请填写并网电压等级",
				"number": "并网电压等级应为大于0的数字",
				"min": "并网电压等级应为大于0的数字"
			},
	   		"electricity_distance": {
				"required": "请填写电网接入点距离",
				"number": "电网接入点距离应为大于0的数字",
				"min": "电网接入点距离应为大于0的数字"
			},

	   		"company_invest": {
				"required": "请填写单位投资",
				"number": "单位投资应为大于0的数字",
				"min": "单位投资应为大于0的数字"
			},
	   		"company_EPC": "请填写EPC厂家",
	   		"capacity_level": "请填写资质等级",

	   		"synchronize_date": {
	   			"required": "请填写并网时间",
	   			"dateISO": "并网时间输入格式不对，应为yyyy-MM-dd"
	   		},
	   		"electricity_data": {
	   			"required": "请填写历史发电量数据",
	   			"number": "历史发电量数据应为大于0的数字",
	   			"min": "历史发电量数据应为大于0的数字"
	   		},
	   		"plan_financing": {
	   			"required": "请填写拟融资金额",
	   			"number": "拟融资金额应为大于0的数字",
	   			"min": "拟融资金额应为大于0的数字"
	   		}
   		},
   		errorClass: 'validate-error',
   		focusInvalid: true,
   		errorPlacement: function(error, element) {
   			// element.focus();
   		},
   		submitHandler: function(form) {
   			$form.ajaxSubmit(options);
   		},
   		invalidHandler: function(event, validator) {
   			try{
   				alert(validator.errorList[0].message);
   			} catch(ex) {
   			}
   		}
	});

	function beforeSubmit(formData, jqForm, options) {
		
		var optype = $form.find("[name=optype]").val();
		if(optype === "save") { // 保存不加校验
			$.loading("正在保存，请稍侯");
			return true;
		}

		var match = jqForm.attr("class").match(/(housetop_nonBuild|housetop_build|ground_nonBuild|ground_build)/),
			state = match && match[1];

		// 二次拦截校验
		switch(state) {
			case "housetop_nonBuild":
			case "housetop_build":
				var $picture_full = $('.housetop_nonBuild_item [name=picture_full]'),
					$picture_full_hiddenId = $('.housetop_nonBuild_item [name=picture_full_hiddenId]');
				if(!$picture_full.val() && !$picture_full_hiddenId.val()) {
					alert("请上传屋顶全景图");
					$picture_full.focus();
					return false;
				}

				var $picture_south = $('.housetop_nonBuild_item [name=picture_south]'),
					$picture_south_hiddenId = $('.housetop_nonBuild_item [name=picture_south_hiddenId]');
				if(!$picture_south.val() && !$picture_south_hiddenId.val()) {
					alert("请上传屋顶正南向照片");
					$picture_south.focus();
					return false;
				}

				var $housetop_property_prove = $(".housetop_nonBuild_item [name=housetop_property_prove]"),
					$housetop_property_prove_hiddenId = $(".housetop_nonBuild_item [name=housetop_property_prove_hiddenId]");
				if(!$housetop_property_prove.val() && !$housetop_property_prove_hiddenId.val()) {
					alert("请上传屋顶产权证明附件");
					$housetop_property_prove.focus();
					return false;
				}

				var $electricity_pay_list = $(".housetop_nonBuild_item [name=electricity_pay_list]"),
					$electricity_pay_list_hiddenId = $(".housetop_nonBuild_item [name=electricity_pay_list_hiddenId]");
				if(!$electricity_pay_list.val() && !$electricity_pay_list_hiddenId.val()) {
					alert("请上传电费单(最近一年)附件");
					$electricity_pay_list.focus();
					return false;
				}
				break;
			case "ground_nonBuild":
			case "ground_build":
				var $picture_full = $('.ground_nonBuild_item [name=picture_full]'),
					$picture_full_hiddenId = $('.ground_nonBuild_item [name=picture_full_hiddenId]');
				if(!$picture_full.val() && !$picture_full_hiddenId.val()) {
					alert("请上传场地情况全景图");
					$picture_full.focus();
					return false;
				}

				var $picture_field = $('.ground_nonBuild_item [name=picture_field]'),
					$picture_field_hiddenId = $('.ground_nonBuild_item [name=picture_field_hiddenId]');
				if(!$picture_field.val() && !$picture_field_hiddenId.val()) {
					alert("请上传场平照片");
					$picture_field.focus();
					return false;
				}

				var $picture_transformer = $('.ground_nonBuild_item [name=picture_transformer]'),
					$picture_transformer_hiddenId = $('.ground_nonBuild_item [name=picture_transformer_hiddenId]');
				if(!$picture_transformer.val() && !$picture_transformer_hiddenId.val()) {
					alert("请上传变电站照片");
					$picture_transformer.focus();
					return false;
				}

				var $rent_time = $('.ground_nonBuild_item [name=rent_time]'),
					$rent_time_val = $.trim($rent_time.val() || "");
				if(!$rent_time_val) {
					alert("请填写租赁年限");
					$rent_time.focus();
					return false;
				} else if( !($rent_time_val && /^\d+(\.\d+)?$/.test($rent_time_val) && parseFloat($rent_time_val) > 0) ) {
					alert("租赁年限应为大于0的数字");
					$rent_time.focus();
					return false;
				}

				var $rent_pay = $('.ground_nonBuild_item [name=rent_pay]'),
					$rent_pay_val = $.trim($rent_pay.val() || "");
				if(!$rent_pay_val) {
					alert("请填写租赁租金");
					$rent_pay.focus();
					return false;
				} else if( !($rent_pay_val && /^\d+(\.\d+)?$/.test($rent_pay_val) && parseFloat($rent_pay_val) > 0) ) {
					alert("租赁租金应为大于0的数字");
					$rent_pay.focus();
					return false;
				}
				break;
		}

		switch(state) {
			case "housetop_nonBuild":
			case "ground_nonBuild":
				var $plan_build_volume = $('.housetop_nonBuild_item [name=plan_build_volume]'),
					$plan_build_volume_val = $.trim($plan_build_volume.val() || "");
				if(!$plan_build_volume_val) {
					alert("请填写拟建设容量");
					$plan_build_volume.focus();
					return false;
				} else if( !($plan_build_volume_val && /^\d+(\.\d+)?$/.test($plan_build_volume_val) && parseFloat($plan_build_volume_val) > 0) ) {
					alert("拟建设容量应为大于0的数字");
					$plan_build_volume.focus();
					return false;
				}

				var $cooperation_type = $('.housetop_nonBuild_item [r-name=cooperation_type]');
				if(!$cooperation_type.filter(":checked").length) {
					alert("与能融网合作方式至少需选中一项");
					$cooperation_type.focus();
					return false;
				}
				break;
			case "housetop_build":
			case "ground_build":
				var $plan_build_volume = $('.housetop_build_item [name=plan_build_volume]'),
					$plan_build_volume_val = $.trim($plan_build_volume.val() || "");
				if(!$plan_build_volume_val) {
					alert("请填写建设容量");
					$plan_build_volume.focus();
					return false;
				} else if( !($plan_build_volume_val && /^\d+(\.\d+)?$/.test($plan_build_volume_val) && parseFloat($plan_build_volume_val) > 0) ) {
					alert("建设容量应为大于0的数字");
					$plan_build_volume.focus();
					return false;
				}

				// 组件
				$("li.component").not(".inverter").children(".item").each(function() {
					var $component = $(this),
						$component_company = $component.find('[r-name=component_company]'),
						$component_type = $component.find('[r-name=component_type]'),
						$component_count = $component.find('[r-name=component_count]');
					if(!$component_company.val()) {
						alert("请填写组件厂家");
						$component_company.focus();
						return false;
					}

					if(!$component_type.val()) {
						alert("请填写组件规格型号");
						$component_type.focus();
						return false;
					}

					var $component_count_val = $.trim($component_count.val() || "");
					if(!$component_count.val()) {
						alert("请填写组件数量");
						$component_count.focus();
						return false;
					} else if( !($component_count_val && /^\d+$/.test($component_count_val) && parseInt($component_count_val) > 0) ) {
						alert("组件数量应为正整数");
						$component_count.focus();
						return false;
					}
				});

				// 逆变器
				$("li.component.inverter").children(".item").each(function() {
					var $inverter = $(this),
						$inverter_company = $inverter.find('[r-name=inverter_company]'),
						$inverter_type = $inverter.find('[r-name=inverter_type]'),
						$inverter_count = $inverter.find('[r-name=inverter_count]');
					if(!$inverter_company.val()) {
						alert("请填写逆变器厂家");
						$inverter_company.focus();
						return false;
					}

					if(!$inverter_type.val()) {
						alert("请填写逆变器规格型号");
						$inverter_type.focus();
						return false;
					}

					var $inverter_count_val = $.trim($inverter_count.val() || "");
					if(!$inverter_count.val()) {
						alert("请填写逆变器数量");
						$inverter_count.focus();
						return false;
					} else if( !($inverter_count_val && /^\d+$/.test($inverter_count_val) && parseInt($inverter_count_val) > 0) ) {
						alert("逆变器数量应为正整数");
						$inverter_count.focus();
						return false;
					}
				});

				var $cooperation_type = $('.housetop_build_item [r-name=cooperation_type]');
				if(!$cooperation_type.filter(":checked").length) {
					alert("与能融网合作方式至少需选中一项");
					$cooperation_type.focus();
					return false;
				}
				break;
		}

		$.loading("正在提交，请稍侯");

	   	return true;
	}

	function successCallback(data) {
		var takeTime = new Date().getTime() - $._loadingDialog.timeStamp;
		setTimeout(function() {
			$.closeLoading();
			if(data.code == "0") {
				var optype = $form.find("[name=optype]").val();
				if(optype === "save") {
					location.href = "?c=ProjectProviderMyPro&a=projectInfoEdit&no=" + data.id + "&token=" + data.idm;
				} else {
					location.href = "?c=ProjectProviderMyPro&a=awaitingAssessment";
				}
			} else {
				// $form.find('[data-type="mul"]').each(function() {
				// 	$(this).attr("name", $(this).attr("name").replace(/^([^\[\]]*)\[\]$/, "$1"));
				// });
				alert(data.msg || "操作失败,请稍后再试！");
			}
		}, takeTime > 1000 ? 0 : 1000 - takeTime);
		
	}

	function failCallback(xhr, status, error, $form) {
		var takeTime = new Date().getTime() - $._loadingDialog.timeStamp;
		setTimeout(function() {
			$.closeLoading();
			if(xhr.status === 413) {
				alert("本次提交的附件太大，请尽量上传小附件，或者分批保存再提交！");
			} else {
				alert("操作失败,请稍后再试！");
			}
		}, takeTime > 1000 ? 0 : 1000 - takeTime);
	}

	$form.find("input[type=submit], input[type=button]").click(function() {
		var optype = $(this).data("optype");
		if(optype === "delete") {
			$.confirm("删除项目后将无法恢复，是否确认删除？").done(function() {
				$.ajax({
					type: $form.attr("method"),
					url: location.href,
					data: {
						optype: optype,
						rtype: 1
					},
					dataType: "json"
				}).done(function(data) {
					if(data.code == "0") {
						location.href = "?c=ProjectProviderMyPro&a=awaitingAssessment";
					} else {
						alert("删除失败！");
					}
				}).fail(function() {
					alert("删除失败！");
				});
			});
			
			return false;
		}

		$form.find("li:hidden input[name], li:hidden select[name]").prop("disabled", true);
		// $form.find('[data-type="mul"]').each(function() {
		// 	$(this).attr("name", $(this).attr("name").replace(/^(.*)$/, "$1[]"));
		// });

		$form.find("[name=optype]").val(optype);
		if(optype === "save") { // 保存不加校验
			$form.ajaxSubmit(options);
			return false;
		}
		return true;
	});

	// $form.ajaxForm(options);

});