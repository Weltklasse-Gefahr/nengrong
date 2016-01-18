$(function() {

	require("common/erqi/dialog");

	$(".l-nav").find(".pushProject").addClass("active")
		.children("a").attr("href", "javascript:;");
	  
	var all_select=0;

	$("#all_select").click(function(){
    if(all_select==0)
	{
		all_select=1;
		$("[name='checkbox']").prop("checked",true);//全选
		return;
	}
	else
	{
		all_select=0;
		$("[name='checkbox']").prop("checked", false);//取消全选
		return;
	}
    });

	
	$("#push_selected").click(function()
	{
		var item_id="";
		$("[name='checkbox']:checked").each(function(){
			item_id+=$(this).parent().parent().data("id")+",";
		})
		if(item_id != "")
		{
			$.confirm('<div id="u139" class="text">'+
				'<p><span>项目编号：</span><span>******</span></p>'+
				'<p><span>融资机构：</span></p>'+
				'<p><span style="visibility:hidden">融资机构：</span><span>国银租赁</span></p>'+
				'<p><span style="visibility:hidden">融资机构：</span><span>确认推送</span></p></div>').done(function()
			{
				$.ajax({
				type: "post",
				url: location.href,
				data: {
					investors: item_id,
					rtype : 1
				},
				dataType: "json"
				}).done(function(data){
					if (data.code== 0)
						{window.location.reload();}
					else 
						{alert('推送失败！');}
				});
				
			}).fail(function() {
			});
		}
    });

	
	$(".bd").on("click", "a", function(){
		if(!$(this).parent().parent().hasClass("div_grey"))
		{
			if(!$(this).parent().siblings().eq(0).children().prop('checked'))
			{
				return;
			}
			var sel_project=$(this);
			$.confirm('<div id="u139" class="text">'+
				'<p><span>项目编号：</span><span>******</span></p>'+
				'<p><span>融资机构：</span></p>'+
				'<p><span style="visibility:hidden">融资机构：</span><span>国银租赁</span></p>'+
				'<p><span style="visibility:hidden">融资机构：</span><span>确认推送</span></p></div>').done(function()
			{
				var item_id="";
				item_id += sel_project.parent().parent().data("id")+",";
				$.ajax({
				type: "post",
				url: location.href,
				data: {
					investors: item_id,
					rtype : 1
				},
				dataType: "json"
				}).done(function(data){
					if (data.code== 0)
						{window.location.reload();}
					else 
						{alert('推送失败！');}
				});
				
			}).fail(function() {
			});

		}
	});


	/* 分页 */
	$(".pager span").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=InnerStaff&a=pushProject&page="+$this.data("page-index");
		}
		return false;
	});
	
});