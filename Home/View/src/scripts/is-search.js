$(function() {
	// 省市区级联
	require("common/erqi/AreaData");
	require("common/erqi/cascadeSelect");
	$('[name="province"]').cascadeSelect(AreaData);

	// 日期选择框
	require("lib/jquery-ui");
	$.datepicker.regional["zh-CN"] = { closeText: "关闭", prevText: "上月", nextText: "下月", currentText: "今天", monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"], monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"], dayNames: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"], dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"], dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"], weekHeader: "周", dateFormat: "yy-mm-dd", firstDay: 1, isRTL: !1, showMonthAfterYear: !0, yearSuffix: "年" };
	$.datepicker.setDefaults($.datepicker.regional['zh-CN']);
	$("input[data-type=date]").datepicker({
		changeMonth: true,
      	changeYear: true
	});

	// 默认跳转到项目信息页
	$(".list .bd a").click(function(e) {
		var data = $(this).parent().parent().data();
		location.href = "?c=InnerStaff&a=projectInfo&no=" + data.id + "&token=" + data.idm;
		return false;
	});

	// 更改项目状态
	require("common/erqi/dialog");
	$(".list .bd .c5 button").click(function(e) {
		var data = $(this).parent().parent().data(),
			oldStatus = data.status,
			$tmp = $('<div><ul class="status-list">\
<li><input id="r1" type="radio" name="status" value="11"/><label for="r1">未提交</label></li>\
<li><input id="r2" type="radio" name="status" value="12"/><label for="r2">已提交</label></li>\
<li><input id="r3" type="radio" name="status" value="13"/><label for="r3">已签意向书</label></li>\
<li><input id="r4" type="radio" name="status" value="14"/><label for="r4">已尽职调查</label></li>\
<li><input id="r5" type="radio" name="status" value="15"/><label for="r5">已签融资合同</label></li>\
</ul></div>');

		$tmp.find('.status-list input[value="' + oldStatus + '"]').attr("checked", "checked");
		$.confirm($tmp.html()).done(function() {
			var status = $(".status-list input:checked").val();
			if(status == oldStatus) {
				alert("项目已处于该状态，请勿重复操作！");
				return;
			}

			$.ajax({
				url: '?c=InnerStaff&a=search&rtype=1',
				data: {
					optype: 'change',
					no: data.id,
					token: data.idm,
					oldStatus: oldStatus,
					status: status
				}
			}).done(function(data) {
				if(data.code == "0") {
					alert("操作成功！");
					location.reload();
				} else {
					alert(data.msg ||　"操作失败！");
				}
			}).fail(function() {
				alert("操作失败！");
			});
		});
	});
});