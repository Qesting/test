var atab = 0;
var newTab = 0;
var num = 0;

$("document").ready(function() {
    $(".card:not(:first-of-type)").addClass("hidden");

    num = $(".card").length;
    num -= 1;
});

$("#next").click(function() {
    newTab = atab + 1;
    if (newTab <= num) {
        $("#alert").removeClass("alert alert-warning").text("");
        
        $("#q"+atab).addClass("hidden");
        $("#q"+newTab).removeClass("hidden");
        
        atab = newTab;
        if (atab == num && $("#sub-grp > *").length == 0) {
            $("#sub-grp").append("<button type='submit' class='btn btn-primary'>Zatwierdź</button>");
        }
    } else {
        $("#alert").text("To już ostatnie pytanie!").addClass("alert alert-warning");
    }
})

$("#prev").click(function() {
    newTab = atab - 1;
    if (newTab >= 0) {
        $("#alert").removeClass("alert alert-warning").text("");

        $("#q"+atab).addClass("hidden");
        $("#q"+newTab).removeClass("hidden");

        atab = newTab;
    } else {
        $("#alert").text("To jest pierwsze pytanie!").addClass("alert alert-warning");
    }
})