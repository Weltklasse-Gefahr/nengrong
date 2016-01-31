$(function(){

	var t=3;//设定跳转的时间
	var showEl = document.getElementById('show');
	var interval = setInterval(function() {
		--t;
	    if(t==0) {
	    	clearInterval(interval);
	        location.href = "?c=User&a=login"; //#设定跳转的链接地址 
	    } else {
	    	showEl.innerHTML = t;
	    }
	}, 1000); //启动1秒定时 
	
	
	$("#jumpbtn").click(function() {
		location.href='?c=User&a=login';
	});
});
