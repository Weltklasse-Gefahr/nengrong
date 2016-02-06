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
					<div class="content-wrap clrfix">\
						<img class="icon" src="/EnergyFe/img/dialog/icon.png" />\
						<div class="content"></div>\
					</div>\
					<div class="control"></div>\
				</div>\
			</div>\
		</div>');

		dialog.appendTo($(document.body));
	}

	$.extend($, {

		_loadingDialog: null,

		loading: function(text) {
			if(!this._loadingDialog) {
				this._loadingDialog = $('<div class="layer-mask">\
							<div class="layer">\
								<div class="hd">\
									<img class="title" src="/EnergyFe/img/dialog/title.png" />\
								</div>\
								<div class="bd" style="width: 423px;">\
									<div class="content-wrap clrfix">\
										<img class="icon" src="/EnergyFe/img/dialog/icon.png" />\
										<div class="content"></div>\
									</div>\
									<div class="control"></div>\
								</div>\
							</div>\
						</div>').appendTo($(document.body));
			}
			this._loadingDialog.show().find(".content").html((text || '加载中，请稍侯') + '<span class="dot">......</span>');
			this._loadingDialog.timeStamp = new Date().getTime();

			var $dot = this._loadingDialog.find(".content .dot"),
				time = 0,
				textArr = [".", "..", "...", "....", ".....", "......"];
			this._loadingDialog.interval = setInterval(function() {
				$dot.text(textArr[time++]);
				if(time === 6) {
					time = 0;
				}
			}, 500);
		},

		closeLoading: function() {
			if(this._loadingDialog && this._loadingDialog.is(":visible")) {
				clearInterval(this._loadingDialog.interval);
				this._loadingDialog.hide();
			}
		},

		confirm: function(opt) {
			opt = opt || {};
			if(typeof opt === "string") {
				opt = {
					content: opt
				};
			}

			init();

			if(opt.width) {
				dialog.find("img.title").css("width", (parseInt(opt.width) + 9) + "px");
			}
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