$("document").ready(function() {
    $("<button id='toTop' class='btn btn-secondary'><span class='bi-arrow-bar-up'></span> <b>Na górę</b></button>").insertAfter("nav > *:last-child");
    $("#toTop").css({
        'position': 'fixed',
        'font-size': '1rem',
        'top' : '70px',
        'z-index' : '9999'
    });

    $("#toTop").click(function() {
        $("html, body").animate({scrollTop: 0}, "slow");
    });

    $(".card p:not(.alert)").css('font-weight', "bold");

    
    $("<hr class='my-5'>").insertBefore("section");
    if($("footer").length == 1) {
        $("<hr class='my-5'>").insertBefore("footer > *:first-child");
    }
})