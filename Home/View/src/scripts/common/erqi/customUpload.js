$(function($) {

	function getPreviewImg(url, skipImg) {
		var match = url.match(/\.([^.]+)$/),
			suffix = match && match[1];
		suffix = suffix && suffix.toLowerCase();
		if(!skipImg && suffix && $.inArray(suffix, ["jpg", "jpeg", "png", "gif", "bmp", "ico"]) !== -1) {
			return url;
		} else {
			var name = {
				"doc": "doc",
				"docx": "doc",
				"pdf": "pdf"
			}[suffix || "doc"];
			return "/EnergyFe/img/" + name + ".png";
		}
	}

	$.fn.customUpload = function(option) {

		option = option || {
			img_url: "attachment.png",
			content: "上传附件",
			uploadType: "file",
			width: "80px",
			height: "20px"
		};

		var suffix = "_hiddenId";

		$.each(this, function(i, item) {

			var $this = $(item);

			if(!$this.is("input[type=file]")) {
				return ;
			}

			var uploadType = option.uploadType,
				$wrap, $inputWrap, $preview;
			
			if(!($this.parent().is(".input-wrap"))) {

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

				$this.parent().after($('<div class="preview" style="display: none;"><a target="_blank" href="javascript:;"></a><i class="del">x</i></div>'));
				if(uploadType === "image") { // 图片预览
					$this.parent().next(".preview").css({
						"width": option.width,
						"height": option.height
					}).find("a").append('<img style="width: '+option.width+';height:'+option.height+'"/>');
				}
			}
			$this.css("visibility", "visible");

			var $inputWrap = $this.parent(),
				$hiddenId = $(),
				$preview = $inputWrap.siblings(".preview");

			// 编辑页预览附件
			var url = $this.attr("data-url");
			if(url) {
				$hiddenId = $('<input type="hidden"' + ($this.data("type") === "mul" ? ' data-type="mul"' : '') + ' name="' + $this.attr("name").replace(/\[\]$/, "") + suffix + ($this.data("type") === "mul" ?'[]' : '') + '" value="' + ($this.attr("data-id") || "") + '" />');
				$this.after($hiddenId);
				var name = $this.attr("data-name"),
					alink = $preview.show().find("a");
				if(uploadType === "image") {
					alink.attr("href", url).find("img").attr({
	                    src: getPreviewImg(url),
	                    alt: name
	                });
					$inputWrap.hide();
				} else {
					alink.attr("href", url).text(name);
				}
				$(this).removeAttr("data-url").removeAttr("data-name").removeAttr("data-id");
			}

			$this.change(function(e) {
				var	resultFile = this.files[0];

				if(option.fileSizeLimit) {
					if(resultFile.size > option.fileSizeLimit.size) {
						alert("该附件太大，有可能导致上传不成功，请尽量选择" + $.bytesToSize(option.fileSizeLimit.size) + "以内的文件，或分批上传！");
						// return false;
					}
				}

				$hiddenId.val("");

				if(resultFile && resultFile.name) {
					var alink = $preview.show().find("a");

					if(uploadType === "image") {
						if(resultFile.type.toLowerCase().match(/^image\/.*/)) {
							var reader = new FileReader();

							reader.onload = function (e) {

		                    	alink.attr("href", this.result).find("img").attr({
				                    src: this.result,
				                    alt: resultFile.name
				                });
				                $inputWrap.hide();

				                option.callback && option.callback.call(item, "add");
			                };

			                reader.readAsDataURL(resultFile);
			            } else {
			            	alink.attr("href", "javascript:;").find("img").attr({
			                    src: getPreviewImg(resultFile.name, true),
			                    alt: resultFile.name
			                });
			            	$inputWrap.hide();
			            	option.callback && option.callback.call(item, "add");
			            }
		        	} else {
		        		alink.attr("href", "javascript:;").text(resultFile.name);
		        		option.callback && option.callback.call(item, "add");
		        	}
				}

			});

			$preview.find(".del").click(function(e) {
				if(uploadType === "image") {
					$preview.hide().find("a").attr("href", "javascript:;").find("img").attr({
	                    src: "",
	                    alt: ""
	                });
				} else {
					$preview.hide().find("a").attr("href", "javascript:;").text("");
				}
				$hiddenId.val("");
				
				$inputWrap.show().find("input[type=file]").val("");
				option.callback && option.callback.call(item, "delete");
				return false;
			});
		});
	};
});