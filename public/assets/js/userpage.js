const changeButton = document.getElementById("changeFake");
const input = document.getElementsByClassName("input");

changeButton.addEventListener('click', function () {
    changeButton.setAttribute("hidden", "true");
    Array.from(input).forEach(function (inputItem) {
        inputItem.removeAttribute("disabled");
        inputItem.removeAttribute("hidden");
    })
})
