const sberChoice = document.getElementById('choice1');
const phoneChoice = document.getElementById('choice2');
const sberImg = document.getElementById('sberImg');
const phoneImg = document.getElementById('phoneImg');

sberChoice.addEventListener('click', function () {
    if (this.checked) {
        sberImg.removeAttribute('hidden');
        phoneImg.setAttribute('hidden', 'true');
        phoneChoice.checked = false;
    }
})

phoneChoice.addEventListener('click', function () {
    if (this.checked) {
        phoneImg.removeAttribute('hidden');
        sberImg.setAttribute('hidden', 'true');
        sberChoice.checked = false;
    }
})
