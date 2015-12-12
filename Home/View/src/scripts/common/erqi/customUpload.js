$(function($) {
	$.fn.customUpload = function(option) {

		option = option || {
			img_url: "",
			content: "+",
			uploadType: "file",
			width: "20px",
			height: "38px"
		};

		$.each(this, function(i, item) {

			var $this = $(item);

			if(!$this.is("input[type=file]")) {
				return ;
			}

			var uploadType, $wrap, $inputFile, $preview;
			
			if(!($this.parent().is(".input-wrap"))) {

				uploadType = option.uploadType;

				$wrap = $('<div class="input-wrap"></div>').css({
					width: option.width,
					height: option.height
				});

				if(option.bg_url) {
					$wrap.css({
						"background-image": "url(/EnergyFe/img/" + option.bg_url + ")"
					});
				}
				if(option.img_url) {
					$wrap.css({
						"line-height": option.height
					}).append('<img src="/EnergyFe/img/'+ option.img_url +'" />');
				}
				if(option.content) {
					$wrap.append(option.content);
				}

				if($this.next().length) {
					$this.next().before($wrap.append($this));
				} else {
					$this.parent().append($wrap.append($this));
				}

				$this.parent().after($('<div class="preview" style="width: '+option.width+';height: '+option.height+';display: none;"><a target="_blank" href="javascript:;"></a><i class="del">x</i></div>'));
				if(uploadType === "image") { // 图片预览
					$this.parent().next(".preview").find("a").append('<img style="width: '+option.width+';height:'+option.height+'"/>');
				}
			}
			$this.css("visibility", "visible");

			var $inputFile = $this.parent(),
				$preview = $inputFile.siblings(".preview");

			$this.change(function(e) {

				var	resultFile = this.files[0];

				if(resultFile && resultFile.name) {
					// if(uploadType === "image") {
						var reader = new FileReader();

						reader.onload = function (e) {
		                    var alink = $preview.show().find("a");

		                    if(uploadType === "image") {
		                    	alink.attr("href", this.result);
			                    alink.find("img").attr({
				                    src: this.result,
				                    alt: resultFile.name
				                });
				                $inputFile.hide();
			                } else {
			                	alink.html(resultFile.name);
			                }

			                option.callback && option.callback.call(item);
		                };

		                reader.readAsDataURL(resultFile);
		        	// } else {
		        	// 	$preview.find("a")
		        	// 	$inputFile.hide();
		        	// }
				}

			});

			$preview.find(".del").click(function(e) {
				$preview.hide();
				$inputFile.show().find("input[type=file]").val("");
				option.callback && option.callback.call(item);
				return false;
			});
		});
	};
});