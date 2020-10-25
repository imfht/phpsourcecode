(function(e) {
	e.confirm = function(b) {
		var c = "";
		e.each(b.buttons, function(b, d) {
			c += '<button class="btn btn-primary ' + d["class"] + '">' + b + "</button>";
			d.action || (d.action = function() {})
		});
		var f = ['<div class="demo-value"><div class="demo-alert"><span class="demo-alert-true">', b.message, '</span></div><div class="modal-footer">', c, "</div></div>"].join("");
		parent.layer.open({
			type: 1,
			title: !1,
			area: ["400px", "162px"],
			offset: "30%",
			closeBtn: 0,
			time: 0,
			content: f,
			success: function(c, d) {
				c.find(".btnOK").on("click", function() {
					var c = b.dataType;
					e.post(b.dataUrl, {
						type: c,
						tip: b.tipName,
						ids: b.dataIds
					}, function(a) {
						parent.layer.close(d);
						1 == a.code ? parent.layer.open({
							type: 1,
							title: !1,
							closeBtn: 0,
							scrollbar: !1,
							shade: 0,
							time: 2E3,
							offset: "55px",
							shift: 5,
							content: '<div class="HTooltip bounceInDown animated" style="width:350px;padding:7px;text-align:center;position:fixed;right:7px;background-color:#5cb85c;color:#fff;z-index:100001;box-shadow:1px 1px 5px #333;-webkit-box-shadow:1px 1px 5px #333;font-size:14px;">' + a.msg + "</div>"
						}) : parent.layer.open({
							type: 1,
							title: !1,
							closeBtn: 0,
							scrollbar: !1,
							shade: 0,
							time: 2E3,
							offset: ["55px", "100%"],
							shift: 6,
							content: '<div class="HTooltip bounceInDown animated" style="width:350px;padding:7px;text-align:center;position:fixed;right:7px;background-color:#D84C31;color:#fff;z-index:100001;box-shadow:1px 1px 5px #333;-webkit-box-shadow:1px 1px 5px #333;font-size:14px;">' + a.msg + "</div>"
						});
						a.url && "" != a.url && setTimeout(function(data) {
							location.reload()
						}, 2E3);
						"" == a.url && setTimeout(function() {
							location.reload()
						}, 1E3)
					})
				});
				c.find(".btnCancel,.close").on("click", function() {
					parent.layer.close(d)
				})
			}
		})
	}
})(jQuery);
