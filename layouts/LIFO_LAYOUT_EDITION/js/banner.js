function getMousePosition(e){
    var eObj = window.event? window.event : e; // IE, FF 에 따라 이벤트 처리 하기
    var mouseX = eObj.clientX;

    var width = $(window).width();
    var bg_position = $("#content-area").css("background-position-x");
    $("#content-area").css("background-position-x", 36 + mouseX / width * 28 + "%");
}

document.onmousemove = getMousePosition;

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})


function openAside() {
  document.getElementById("aside").style.height = "100%";

}

function closeAside() {
  document.getElementById("aside").style.height = "0%";
}
