$(function() {

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
		var str="";
		$("[name='checkbox']:checked").each(function(){
			str+=$(this).parent().parent().data("id")+"&";
		})
		alert(str);
    });

	require("common/erqi/dialog");
	$(".bd").on("click", "a", function(){
		if(!$(this).parent().parent().hasClass("div_grey"))
		{
			if(!$(this).parent().siblings().eq(0).children().prop('checked'))
			{
				return;
			}
			$.confirm('<div id="u139" class="text">'+
				'<p><span>项目编号：</span><span>******</span></p>'+
				'<p><span>融资机构：</span></p>'+
				'<p><span style="visibility:hidden">融资机构：</span><span>国银租赁</span></p>'+
				'<p><span style="visibility:hidden">融资机构：</span><span>确认推送</span></p></div>').done(function()
			{
				
			}).fail(function() {
			});

			var str="";
			str+=$(this).parent().parent().data("id")+"&";
			alert(str);
		}
	});

	$(".pushButton1").click(function(){ 
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_reset_id").off("click.reset");
		$("#confirm_reset_id").on("click.reset", function(){
			$.ajax({
		    type: "post",
		    url: "?c=Admin&a=resetPassword" ,
		    data: {
				id: item_id
		    },
			dataType: "json"
			}).done(function(data){
				if (data.code== 0)
					{$("#myAlert_reset_success").show();}
				else 
					{$("#myAlert_reset_failed").show();}
			});
		});

		
	});

	
});