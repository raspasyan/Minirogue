$(document).ready(function(){
    $(document).mouseover(function(e) {
        var _this = e.target;
        if ($(_this).hasClass("icon")) toggleTip(_this);
    });

    $(document).mouseout(function(e) {
        var _this = e.target;
        if ($(_this).hasClass("icon")) toggleTip(_this);
    });

    function toggleTip(element) {
        $(element).toggleClass("active");
    }

    $(".donation").click(function(e){
        console.log(1);
        $(this).toggleClass("show");
    });
});