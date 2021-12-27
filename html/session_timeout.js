function logout(){
    alert("Ваша сессия завершена из-за отсутствия активности");
    $.ajax({
        type: "GET",
        url: "http://site.local/logout.php", 
        success: function () {
            window.location.replace("http://site.local/auth.php");
        }
    });
}
let logoutTimer = setTimeout(logout, 120000);


$(document).mousemove(function () { 
    clearTimeout(logoutTimer);
    logoutTimer = setTimeout(logout, 120000);
});


