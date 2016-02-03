$(function() {

	$(".l-nav").find(".myInformation").addClass("active");
	require("lib/jquery.form");
	

	// 保存资料
	var options = {
		data: {
			rtype: 1
		},
	   	// target: '#output',          //把服务器返回的内容放入id为output的元素中      
	   	beforeSubmit: beforeSubmit, //提交前的回调函数  
	   	success: successCallback,  	//提交后的回调函数
	   	dataType: "json",           //html(默认), xml, script, json...接受服务端返回的类型  
	   	// clearForm: true,         //成功提交后，清除所有表单元素的值  
	   	// resetForm: true,         //成功提交后，重置所有表单元素的值  
	   	timeout: 6000               //限制请求的时间，当请求大于3秒后，跳出请求
	};
	  
	function beforeSubmit(formData, jqForm, options) {
	   	return true;
	}

	function successCallback(data) {
		if(data.code == "0") {
			alert("保存成功！");
			location.href="?c=ProjectInvestorMyPro&a=recommendedProject";
		} else {
			alert(data.msg || "保存失败！");
		}
	}

	$form = $("#infoForm");
	$form.validate({
		ignore: ':hidden',
		rules: {
			"company_contacts": "required",
			"company_contacts_phone": {
				"required": true,
				"mobile": true
			}
		},
		messages: {
			"company_contacts": "请填写联系人",
			"company_contacts_phone": {
				"required": "请填写联系人手机",
				"mobile": "手机号格式不对"
			}
		},
		errorClass: 'validate-error',
		focusInvalid: false,
   		errorPlacement: function(error, element) {
   			element.focus();
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

	// $("#infoForm").ajaxForm(options);
});