$(document).ready(function(){
    
    $(".accordion").click(function(){
        var _targetPanel = $(this).attr("targetPanel");
        
        if ($(this).hasClass("active")) {
            $("."+_targetPanel).css({display: "none"});
        } else {
            $("."+_targetPanel).css({display: "block"});
        }
        
        $(this).toggleClass("active");
    });
});