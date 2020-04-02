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

    if($(".form--plain__box--hideable").length) {
        $(document).on("change", ".switcher input[type='checkbox']", function() {
            var target = $(this).data("target");
            $(".form--plain__box--hideable").removeClass("visible");
            $("#" + target).addClass("visible");
            $(".switcher input[type='checkbox']").not($(this)).prop("checked", false);
            $(".switcher input[type='checkbox']").not($(this)).parent().parent().removeClass("checked")
            $(this).parent().parent().addClass("checked");
            if (!$(this).is(":checked")){
                $(".form--plain__box--hideable").removeClass("visible");
                $(".form--plain__box--hideable.default").addClass("visible");
                $(this).parent().parent().removeClass("checked");
            }
        })
    }

    $(document).on("change", ".js-show-target", function() {
        var target = $(this).data("target");
        if ($(this).hasClass("target-visible")){
            $("#" + target).hide();
            $(this).removeClass("target-visible")
        } else {
            $("#" + target).show();
            $(this).addClass("target-visible")
        }
    })
    if($('.js-add-element__element').length){
        var element = $('.js-add-element__element');
        var elementCount = 0;
    }
    $(document).on("click", ".js-add-element", function(e) {
        e.preventDefault;
        elementCount = elementCount + 1;
        var wrapper = $(this).parent().find("js-add-element__wrapper");
        element.clone().attr("data-count", elementCount).appendTo($(".js-add-element__wrapper"));
    })

    if($(".js-select").length){
        $(".js-select").each(function(){
            var optionsCount = $(this).find("option").length;
            $(this).after("<div class='js-select--new'><span class='close'></span><ul></ul></div>");
            for(var i=0; i<optionsCount ; i++){
                if(i == 0) {
                    $(this).next(".js-select--new").find("ul").append("<li class='active'>"+ $(this).find("option").eq(i).text() +"</li>")
                } else {
                    $(this).next(".js-select--new").find("ul").append("<li>"+ $(this).find("option").eq(i).text() +"</li>")
                }
            }
        })
    }

    $(document).on("click", ".js-select--new", function(e) {
        var target = $(e.target);
        if (!$(this).hasClass("js-select--new--open")) {
            $(this).addClass("js-select--new--open");
        } else {
            $(this).removeClass("js-select--new--open");
        }

        if(target.is("li")){
            target.parent().find("li").removeClass("active")
            target.addClass("active");
        }
    })

});
