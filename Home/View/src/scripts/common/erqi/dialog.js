$(function($) {

	var dialog;
	function init() {
		if(dialog) {
			return;
		}
		dialog =  $('<div class="layer-mask">\
			<div class="layer">\
				<div class="hd">\
					<img class="title" src="/EnergyFe/img/dialog/title.png" />\
				</div>\
				<div class="bd">\
					<div class="content-wrap">\
						<img class="icon" src="/EnergyFe/img/dialog/icon.png" />\
						<span class="content"></span>\
					</div>\
					<div class="control"></div>\
				</div>\
			</div>\
		</div>');

		dialog.appendTo($(document.body));
	}

	$.extend($, {
		confirm: function(opt) {
			opt = opt || {};
			if(typeof opt === "string") {
				opt = {
					content: opt
				};
			}

			init();

			dialog.find(".layer").css("width", opt.width || "423px")
						.find(".content").html(opt.content);
			dialog.find(".control").html('\
				<button class="cancel js-cancel">'+ (opt.cancelText || "否") +'</button>\
				<button class="ok js-ok last">'+ (opt.okText || "是") +'</button>');
			dialog.show();

			var promise = $.Deferred();
			dialog.find(".js-ok").click(function(){
				dialog.hide();
				promise.resolve();
			});
			dialog.find(".js-cancel").click(function(){
				dialog.hide();
				promise.reject();
			});

			
			return promise;
		}
	})
});