var atab;
var num = 0;
var p;
var grp;

window.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".card:not(:first-of-type)");

    for (let el of tabs) {
        el.classList.add("hidden");
    }

    atab = 0;

    p = document.querySelector("p#alert");
    grp = document.getElementById("sub-grp");

    const all = document.querySelectorAll(".card");
    for (let el of all) {
        num += 1;
    }
    num -= 1;
}, false);

document.getElementById("next").addEventListener("click", function() {
    newTab = atab + 1;
    if (newTab <= num) {
        p.classList.remove("alert", "alert-warning");
        p.textContent = "";
        
        document.getElementById(atab).classList.add("hidden");
        document.getElementById(newTab).classList.remove("hidden");
        
        atab = newTab;
        if (atab == num && grp.childElementCount == 0) {
            const inp = document.createElement("input");
            let btn = grp.appendChild(inp);
            btn.value = "Zatwierdź";
            btn.classList.add("btn", "btn-primary");
            btn.type = "submit";
        }
    } else {
        p.textContent = "To już ostatnie pytanie!";
        p.classList.add("alert", "alert-warning");
    }
})

document.getElementById("prev").addEventListener("click", function() {
    newTab = atab - 1;
    if (newTab >= 0) {
        p.classList.remove("alert", "alert-warning");
        p.textContent = "";

        document.getElementById(atab).classList.add("hidden");
        document.getElementById(newTab).classList.remove("hidden");

        atab = newTab;
    } else {
        p.textContent = "To jest pierwsze pytanie!";
        p.classList.add("alert", "alert-warning");
    }
})