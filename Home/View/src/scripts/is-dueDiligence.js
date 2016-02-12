$(function() {

	$(".l-nav").find(".dueDiligence").addClass("active")
		.children("a").attr("href", "javascript:;");

	// 省市区级联
	require("common/erqi/AreaData");
	require("common/erqi/cascadeSelect");
	$(".base-info .area select").cascadeSelect(AreaData);

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

	$(".result-info .s").click(function(e) {
		$(".result-info .s").removeClass("active");
		$(this).addClass("active");
		$("input[name=evaluation_result]").val(e.target.className.replace(/^s-/, "").toUpperCase());
	});

	// 上传附件
	$(".part3 input[type=file]").uploadifive({

		'fileObjName': 'attachment',
		//后台处理的页面
        'uploadScript': '?c=InnerStaff&a=dueDiligence&optype=upload&rtype=1',

        'buttonClass': 'uploadifive-mf',
        'buttonText': '<img class="attachment-logo" src="/EnergyFe/img/attachment.png">上传附件',

        'fileSizeLimit': '10MB',

        'dropTarget': '.part3',
        'height': '34px',
       
        //上传文件页面中，你想要用来作为文件队列的元素的id, 默认为false  自动生成,  不带#
        'queueID': 'fileQueue',

        'itemTemplate': '<div class="uploadifive-queue-item">\
<a class="close" href="#">删除</a>\
<div><img class="attachment-logo" src="/EnergyFe/img/attachment.png">\
<span class="filename"></span>\
<span class="filesize"></span>\
<span class="fileinfo"></span></div>\
<div class="progress"><div class="progress-bar"></div></div>\
</div>',

        'fileType' : 'image/*,\
application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,\
.docx,xlsx,pptx,\
text/plain,application/pdf,\
application/zip,application/x-zip-compressed,\
rar,arj,gz,z,tar,7z,gzip,bz2',

        overrideEvents: ['onUploadComplete'],

        onAddQueueItem: function(file) {
        	file.queueItem.find(".filesize").html("（" + $.bytesToSize(file.size) + "）");
        	file.queueItem.find(".filename").attr("title", file.name);
        },

        onUploadComplete: function(file, data) {
            file.queueItem.find('.progress-bar').css('width', '100%');
            // file.queueItem.find('.fileinfo').html(' - Completed');
            file.queueItem.find('.progress').slideUp(250);
            file.queueItem.addClass('complete');
        	
        	var obj = JSON.parse(data);
	      	if (obj.code == "0") {
	      		console.log && console.log("上传"+file.name+"完成！");
	      		// file.queueItem.find('.fileinfo').html(' - 成功');
	      		file.queueItem.find('.fileinfo').html('');
	    		$(file.queueItem).append($('<input type="hidden" name="doc_mul[]" value="' + obj.id + '" />'));
	      	} else {
	      		alert("上传 " + file.name + "失败！\n" + obj.msg);
	      		file.queueItem.find('.fileinfo').html('<span style="color: red;"> - 失败</span>');
	        	// document.getElementById("submit").disabled = false;
      		}
        },

        onCancel: function(file) {
      		/* 注意：取消后应重新设置uploadLimit */
      		$data = $(this).data('uploadifive'),
      		$data.settings.uploadLimit++;
      		console.log && console.log(file.name + " 已取消上传~!");
    	},

        onFallback: function() {
      		alert("浏览器太老，该页面部分功能将无法使用,\n请使用现代浏览器访问，如chrome、firefox!");
    	}
    });

	require("lib/jquery.form");

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

		// if($("#submit").hasClass("disabled")) {
		// 	return false;
		// }

		var optype = $form.find("[name=optype]").val();
		if(optype === "save") { // 保存不加校验
			return true;
		}

	   	// $("#submit").addClass("disabled");

	   	return true;
	}

	function successCallback(data) {
		// $("#submit").removeClass("disabled");
		var optype = $form.find("[name=optype]").val(),
			optext = optype === "save" ? "保存" : "提交";
		if(data.code == "0") {
			alert(optext+"成功！");
			if(optype === "save") {
				$(".operator .tips").css("visibility", "visible");
			} else {
				location.reload();
			}
		} else {
			alert(data.msg || (optext+"失败！"));
		}
	}

	// 编辑页删除之前已上传的附件
	$(".uploadifive-queue-item-init .close").click(function() {
		$(this).parent().remove();
		return false;
	});

	$form = $("#infoForm");
	$form.validate({
		ignore: ':hidden, [data-with-file="true"]',
		rules: {
			"IRR": {
				"required": true,
   				"number": true,
   				"min": 0
			},
			"static_payback_time": {
				"required": true,
   				"number": true,
   				"min": 0
			},
			"dynamic_payback_time": {
				"required": true,
   				"number": true,
   				"min": 0
			},
			"LCOE": {
				"required": true,
   				"number": true,
   				"min": 0,
   				"max": 1
			},
			"npv": {
				"required": true,
   				"number": true,
   				"min": 0
			},
			"power_asset_current_value": {
				"required": true,
   				"number": true,
   				"min": 0
			},

			"housetop_owner": "required",
			"plan_build_volume": "required",
			"province": "required",
			"city": "required",
			// "county": "required",
			"project_address": "required",
			"housetop_type_other": {
   				"required": function() {
   					if($('[name="housetop_type"]').is(":visible") && $('[name="housetop_type"]').val() == "0") {
   						return true;
   					}
   					return false;
	   			}
	   		},
	   		"plan_financing": {
	   			"required": true,
	   			"number": true,
	   			"min": 0
	   		},

			"project_name": "required",
			"project_finish_date": {
				"required": true,
	   			"dateISO": true
			},
			"project_electricity_price": {
				"required": true,
	   			"number": true,
	   			"min": 0
			},
			"ground_property_other": {
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
   			"project_investment": {
   				"required": true,
   				"number": true,
   				"min": 0
   			},
   			"document_review": "required",
   			"project_quality_situation": "required",
   			"project_invest_situation": "required",
   			"project_earnings_situation": "required"
		},
		messages: {
			"IRR": {
				"required": "请填写内部收益率",
				"number": "内部收益率应为大于0的数字",
				"min": "内部收益率应为大于0的数字"
			},
			"static_payback_time": {
				"required": "请填写静态投资回收年",
   				"number": "静态投资回收年应为大于0的数字",
   				"min": "静态投资回收年应为大于0的数字"
			},
			"dynamic_payback_time": {
				"required": "请填写动态投资回收年",
   				"number": "动态投资回收年应为大于0的数字",
   				"min": "动态投资回收年应为大于0的数字"
			},
			"LCOE": {
				"required": "请填写LCOE",
   				"number": "LCOE应为[0,1]之间的数字",
   				"min": "LCOE应为[0,1]之间的数字",
   				"max": "LCOE应为[0,1]之间的数字"
			},
			"npv": {
				"required": "请填写净现值npv",
   				"number": "净现值npv应为大于0的数字",
   				"min": "净现值npv应为大于0的数字"
			},
			"power_asset_current_value": {
				"required": "请填写电站资产累计现值",
   				"number": "电站资产累计现值应为大于0的数字",
   				"min": "电站资产累计现值应为大于0的数字"
			},

			"housetop_owner": "请填写屋顶业主名称",
			"plan_build_volume": "请填写建设容量",
			"province": "请选择省份",
			"city": "请选择市",
			// "county": "请选择区",
			"project_address": "请填写详细地址",
			"housetop_type_other": "请填写屋顶类型",
			"plan_financing": {
	   			"required": "请填写拟融资金额",
	   			"number": "拟融资金额应为大于0的数字",
	   			"min": "拟融资金额应为大于0的数字"
	   		},

			"project_name": "请填写项目名称",
			"project_finish_date": {
				"required": "请填写项目完工时间",
	   			"dateISO": "项目完工时间输入格式不对，应为yyyy-MM-dd"
			},
			"project_electricity_price": {
				"required": "请填写项目电价",
	   			"number": "项目电价应为大于0的数字",
	   			"min": "项目电价应为大于0的数字"
			},
			"ground_property_other": "请填写土地性质",
			"ground_area": {
				"required": "请填写租赁土地面积",
				"number": "租赁土地面积应为大于0的数字",
				"min": "租赁土地面积应为大于0的数字"
			},
			"project_investment": {
   				"required": "请填写项目总投资",
   				"number": "项目总投资应为大于0的数字",
   				"min": "项目总投资应为大于0的数字"
   			},
   			"document_review": "请填写文件审查",
   			"project_quality_situation": "请填写工程建设可行性",
   			"project_invest_situation": "请填写项目建设投资情况",
   			"project_earnings_situation": "请填写项目经济收益情况"
		},
		errorClass: 'validate-error',
   		focusInvalid: false,
   		errorPlacement: function(error, element) {
   			element.focus();
   		},
   		submitHandler: function(form) {
   			// 二次拦截校验
			if(!$("input[name=evaluation_result]").val()) {
				alert("请选择评价结果");
				$("[name=evaluation_result_mark]").focus();
				return false;
			}
			
   			$.confirm("提交后无法修改，是否确认提交？").done(function() {
				$form.ajaxSubmit(options);
			});
   		},
   		invalidHandler: function(event, validator) {
   			try{
   				alert(validator.errorList[0].message);
   			} catch(ex) {
   			}
   		}
   	});

	var param = $.parseQueryParam();
	$form.attr("action", $form.attr("action")+"&no="+param.no+"&token="+param.token);
	$form.find("input[type=submit], input[type=button]").click(function() {
		var optype = $(this).data("optype");
		
		$form.find("[name=optype]").val(optype);

		if(optype === "save") {
			$form.ajaxSubmit(options);
			return false;
		}

		return true;
	});
});