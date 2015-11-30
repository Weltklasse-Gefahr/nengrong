$(function($) {
	$.fn.customInputFile = function() {

		$.each(this, function(i, item) {

			var $this = $(this);

			if(!$this.is("input[type=file]")) {
				return ;
			}
			
			if(!($this.parent().is(".input-file"))) {
				$this.wrap($('<div class="input-file"></div>'));
				$this.parent().before($('<div class="preview" style="display: none;"><img /><i class="del">x</i></div>'));
			}

			var $inputFile = $this.parent(),
				$preview = $inputFile.prev();

			$this.change(function(e) {

				var	resultFile = this.files[0];

				if(resultFile && resultFile.name) {
					var reader = new FileReader();

					reader.onload = function (e) {
	                    $preview.show().find("img").attr({
	                    	src: this.result,
	                    	alt: resultFile.name
	                    });

	                    $inputFile.hide();
	                };

	                reader.readAsDataURL(resultFile);
				}

			});

			$preview.find(".del").click(function(e) {
				$preview.hide();
				$inputFile.show().find("input[type=file]").val("");
			});
		});
	};
});