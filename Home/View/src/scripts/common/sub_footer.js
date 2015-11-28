;(function(){
	var material_Contentbox = $(".material_Contentbox"),
		black = $(".black");
	if(material_Contentbox.length && black.length) {
		material_Contentbox.height($(document).height() - $("#topNav").height() - black.height());
	}
}());