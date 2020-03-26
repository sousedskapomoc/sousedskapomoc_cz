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

    $(document).on("scroll", function() {
        if (($(".js-scroll-fix").length) && ($(window).width() > 991)) {
            var el = $(".js-scroll-fix");
            var elWrapper = $(".js-scroll-fix-wrapper");
            var footerHeight = $(".footer").outerHeight(true);
            var elOffset = elWrapper.offset().top;
            var scrollTop = $(window).scrollTop();

            if(scrollTop >= elOffset) {
                el.addClass("pos--fixed right top");
                el.width(elWrapper.width());

                if((scrollTop + $(window).height() - ($(window).height() - el.outerHeight(true))) >= ($(document).height() - footerHeight)) {
                    el.addClass("bottom-of-page");
                } else {
                    el.removeClass("bottom-of-page");
                }
            } else {
                el.removeClass("pos--fixed right top")
            }
        }
    })

    $(window).on("resize", function() {
        if (($(".js-scroll-fix").length) && ($(window).width() > 991)) {
            var el = $(".js-scroll-fix");
            var elWrapper = $(".js-scroll-fix-wrapper");
            var footerHeight = $(".footer").outerHeight(true);
            var elOffset = elWrapper.offset().top;
            var scrollTop = $(window).scrollTop();
            if(scrollTop >= elOffset) {
                el.addClass("pos--fixed right top");
                el.width(elWrapper.width());
                if((scrollTop + $(window).height()) >= ($(document).height() - footerHeight)) {
                    el.addClass("bottom-of-page");
                } else {
                    el.removeClass("bottom-of-page");
                }
            } else {
                el.removeClass("pos--fixed right top")
            }
        } else {
            $(".js-scroll-fix").removeClass("pos--fixed right top");
            $(".js-scroll-fix").width($(".js-scroll-fix").parent().width());
        }
    })

    if (($(".js-scroll-fix").length) && ($(window).width() > 991)) {
        var el = $(".js-scroll-fix");
        var elWrapper = $(".js-scroll-fix-wrapper");
        var footerHeight = $(".footer").outerHeight(true);
        var elOffset = elWrapper.offset().top;
        var scrollTop = $(window).scrollTop();

        if(scrollTop >= elOffset) {
            el.addClass("pos--fixed right top");
            el.width(elWrapper.width()-$(".footer"));
            if((scrollTop + $(window).height()) >= ($(document).height() - footerHeight)) {
                el.addClass("bottom-of-page");
            } else {
                el.removeClass("bottom-of-page");
            }
        } else {
            el.removeClass("pos--fixed right top")
        }
    }
});
