$(function($) {
	$.fn.customInputFile = function() {

		$.each(this, function(i, item) {

			var $this = $(item);

			if(!$this.is("input[type=file]")) {
				return ;
			}

			var fileType, $inputFile, $preview
			
			if(!($this.parent().is(".input-wrap"))) {

				fileType = $(this).data("type") || "file";

				if(fileType === "image") { // 图片预览
					$this.wrap($('<div class="input-wrap input-' + fileType + '"></div>'));
					$this.parent().before($('<div class="preview" style="display: none;"><a target="_blank" href="javascript:;"><img /></a><i class="del">x</i></div>'));
				} else { // 文件展示文件名
					$this.wrap($('<div class="input-wrap input-' + fileType + '">+</div>'));
					$this.parent().after($('<div class="preview" style="display: none;"><a target="_blank" href="javascript:;"></a><i class="del">x</i></div>'));
				}
			}
			$this.css("visibility", "visible");

			var $inputFile = $this.parent(),
				$preview = $inputFile.siblings(".preview");

			$this.change(function(e) {

				var	resultFile = this.files[0];

				if(resultFile && resultFile.name) {
					// if(fileType === "image") {
						var reader = new FileReader();

						reader.onload = function (e) {
		                    var alink = $preview.show().find("a");

		                    if(fileType === "image") {
		                    	alink.attr("href", this.result);
			                    alink.find("img").attr({
				                    src: this.result,
				                    alt: resultFile.name
				                });
				                $inputFile.hide();
			                } else {
			                	alink.html(resultFile.name);
			                }
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
				return false;
			});
		});
	};
});