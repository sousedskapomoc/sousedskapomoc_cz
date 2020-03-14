$(function(){
    
    $(document).on("click", ".burger-area", function(){
       var list = $(this).parent().find("ul");

       if(!list.hasClass("show")){
           list.addClass("show");
           $(this).addClass("burger-active")
       } else {
           list.removeClass("show");
           $(this).removeClass("burger-active")
       }
    })

    $(document).click(function(event) {
        //if you click on anything except the modal itself or the "open modal" link, close the modal
        if (!$(event.target).closest(".navigation").length) {
            $(".navigation ul").removeClass("show");
            $(".burger-area").removeClass("burger-active")
        }
    });
});
