$(document).ready(function(){
    
//redimensionamento de texto
    $(window).bind('resize', function()
        {
            //Standard height, for which the body font size is correct
            var logo = $("#course-header .heading-container");
            var width = logo.width();
            var height = logo.height();
	    console.log(width);
	    console.log(height);
            //if(width < 900) return;
            var newFontSize = width * .03 - 1;
            var newHeightSize = height * .06 - 1;
	    var hi = $(".heading-info");
            hi.css("font-size", newFontSize + 'px');
            hi.css("line-height", newHeightSize + 'px');
        }).trigger('resize');
 
});
