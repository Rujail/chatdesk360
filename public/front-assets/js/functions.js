$(document).ready(function() {
    //Lazy Load
    $('.lazy').lazy();

     //responsive menu
    $(".menu-bottom").on("click", function() {
        $("html").toggleClass("menu-open")
    });
    $(".menu-bottom").click(function() {
        $(this).toggleClass("click")
    });

    AOS.init({disable: 'mobile'});

    //Form Validate
    
    $(".validate-popupform").validate();
    $(".validate-contact").validate();
    

    $('input[type="email"], input[type="tel"], input[type="text"], textarea, input[type="number"], select').on('input', function() {
        $(this).val($(this).val().replace(/^\s+/, ''));
    });


    //Owl Carousel
    $('.owl-testimonial').owlCarousel({
        loop:true,
        nav:true,
        dots:false,
        margin:20, 
        responsiveClass:true,
        autoplay:true,
        autoplayTimeout:3000,
        autoplaySpeed: 800,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:1,
            },
            1000:{
                items:1,
            },
            360:{
                items:1,
            }
        }
    });
    $('.owl-site-logo').owlCarousel({
        loop:true,
        nav:false,
        dots:false,
        margin:20, 
        responsiveClass:true,
        autoplay:true,
        autoplayTimeout:3000,
        autoplaySpeed: 800,
        responsive:{
            0:{
                items:3,
            },
            600:{
                items:3,
            },
            1000:{
                items:6,
            },
            360:{
                items:3,
            }
        }
    });

    
   
});




