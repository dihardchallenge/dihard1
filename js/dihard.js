function hideshow(eid) {
    var x = document.getElementById(eid);
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}