$(function() {

	$(".l-nav").find(".myInformation").addClass("active");
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
			alert("修改成功！");
		} else {
			alert("修改失败！\n"+data.errmsg);
		}
	}

	$("#infoForm").ajaxForm(options);
});