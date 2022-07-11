var content = "";
            
function ajax() {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) content = this.responseText;
    }

    request.open("GET", "quote.php", true)
    request.send();
}

ajax(); // automatyczne wywołanie zapytania

var qv = false;
$(".navbar-brand").css('cursor', 'pointer');
$(".navbar-brand").click(function() {
    if (!qv) {

        qv = true;
        $(".navbar-brand").css('cursor', 'auto');
        
        $("<div class='container'><p class='alert alert-info' id='q'></p></div>").insertBefore(".wrapper > :first-child");
        
        $("#q").text(content);
        $("#q").delay(2000).fadeOut(400);
        
        setTimeout(function() {
            qv = false;
            $("#q").remove();
            $(".navbar-brand").css('cursor', 'pointer');
        }, 2500);

        ajax(); // ponowne wywołanie (odświeżenie zmiennej content :string)
    }
});