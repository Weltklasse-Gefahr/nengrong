$(function() {

	$(".l-nav").find(".dueDiligence").addClass("active")
		.children("a").attr("href", "javascript:;");

	// 上传附件
	$(".part3 input[type=file]").uploadifive({

		'fileObjName': 'attachment',
		//后台处理的页面
        'uploadScript': '?c=InnerStaff&a=dueDiligence&optype=upload&rtype=1',

        'buttonClass': 'uploadifive-mf',
        'buttonText': '<img class="attachment-logo" src="/EnergyFe/img/attachment.png">上传附件',

        'dnd': false,
        'height': '34px',
       
        //上传文件页面中，你想要用来作为文件队列的元素的id, 默认为false  自动生成,  不带#
        'queueID': 'fileQueue',

        'itemTemplate': '<div class="uploadifive-queue-item error">\
<a class="close" href="#">删除</a>\
<div><img class="attachment-logo" src="/EnergyFe/img/attachment.png">\
<span class="filename"></span>\
<span class="filesize"></span>\
<span class="fileinfo"></span></div>\
<div class="progress"><div class="progress-bar"></div></div>\
</div>',

        'fileType' : '*',

        'overrideEvents': ['onProgress', 'onUploadComplete'],

        'onAddQueueItem': function(file) {
        	alert(1);
        },

        'onUploadComplete': function(file, data) {
        	var obj = JSON.parse(data);
	      	if (obj.img == "500") {
	        	alert("系统异常！");
	      	} else {
	        	$("#frontSide").val(obj.img);
	        	document.getElementById("submit").disabled = false;
      		}
        },

        onCancel: function(file) {
       		$("#frontSide").val("");
      		/* 注意：取消后应重新设置uploadLimit */
      		$data	= $(this).data('uploadifive'),
      		$data.settings.uploadLimit++;
      		alert(file.name + " 已取消上传~!");
    	},

        'onFallback' : function() {
      		alert("浏览器太老，该页面部分功能将无法使用,\n请使用现代浏览器访问，如chrome、firefox!");
    	},
    	'onUpload' : function(file) {
    		$("#submit").addClass("disabled");
    	}
    });

	require("lib/jquery.form");

	// 保存资料
	var options = {
	   	// target: '#output',          //把服务器返回的内容放入id为output的元素中      
	   	beforeSubmit: beforeSubmit, //提交前的回调函数  
	   	success: successCallback,  	//提交后的回调函数
	   	dataType: "json"           //html(默认), xml, script, json...接受服务端返回的类型  
	   	// clearForm: true,         //成功提交后，清除所有表单元素的值  
	   	// resetForm: true,         //成功提交后，重置所有表单元素的值  
	   	// timeout: 6000               //限制请求的时间，当请求大于3秒后，跳出请求
	};
	  
	function beforeSubmit(formData, jqForm, options){

		if($("#submit").hasClass("disabled")) {
			return false;
		}

	   	//formData: 数组对象，提交表单时，Form插件会以Ajax方式自动提交这些数据，格式如：[{name:user,value:val },{name:pwd,value:pwd}]  
	   	//jqForm:   jQuery对象，封装了表单的元素
	   	//options:  options对象
	   	var queryString = $.param(formData);   //name=1&address=2  
	   	var formElement = jqForm[0];              //将jqForm转换为DOM对象  
	   	var mobile = $.trim(formElement.company_contacts_phone.value);

	   	// if(!mobile) {
	   	// 	alert("请输入联系人手机号");
	   	// 	return false;
	   	// }

	   	$("#submit").addClass("disabled");

	   	return true;
	}

	function successCallback(data) {
		$("#submit").removeClass("disabled");
		if(data.code == "0") {
			alert("上传成功！");
			location.href="?c=ProjectProviderMyPro&a=awaitingAssessment";
		} else {
			alert(data.msg || "上传失败！");
		}
	}

	$("#infoForm").ajaxForm(options);
});