/*
 * 	Easy Tooltip 1.0a - jQuery plugin
 *	written by Alen Grakalic	
 *  --- Hacked by Ve Bailovity (Incsub) ---
 *	http://cssglobe.com/post/4380/easy-tooltip--jquery-plugin
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
(function($) {

	$.fn.easyTooltip = function(options){
	  
		// default configuration properties
		var defaults = {	
			xOffset: 10,		
			yOffset: 25,
			tooltipId: "easyTooltip",
			clickRemove: false,
			content: "",
			useElement: ""
		}; 
			
		var options = $.extend(defaults, options);  
		var content;
				
		this.each(function() {  
			var $me = $(this);
			var title = $me.attr("title");
			if (!title) return true;
			$me.hover(
				function (e) {
					var left = (e.pageX + options.xOffset);
					$me.attr("title", "");
					if ($("#" + options.tooltipId).length) $("#" + options.tooltipId).remove();
					$("body").append("<div id='"+ options.tooltipId + "'>" + title + "</div>");		
					$("#" + options.tooltipId).css("position", "absolute");
					
					var height = $("#" + options.tooltipId).height();
					var top = e.pageY - options.yOffset;
					if (top - height <= $(window).scrollTop() + 28) {
						top = e.pageY + options.yOffset;
						$("#" + options.tooltipId).addClass("reverse");
					} else top = top - height;
					$("#" + options.tooltipId).css({
						"top": top,
						"left": (e.pageX + options.xOffset)
					});
				},
				function (e) {
					$("#" + options.tooltipId).remove();
					$me.attr("title", title);
				}
			);
		});
	  
	};

})(jQuery);