const formReg = document.getElementById('signUp');
const formAuth = document.getElementById('signIn');
const checkReg = document.getElementById('reg');
const checkAuth = document.getElementById('auth');

checkAuth.addEventListener('click', function (event) {
    if (this.checked) {
        formAuth.removeAttribute('hidden');
        checkAuth.setAttribute('disabled', 'true');
        formReg.setAttribute('hidden', 'true');
        checkReg.removeAttribute('disabled');
        checkReg.checked = false;
        Array.from(document.getElementsByClassName('message')).forEach(function (message) {
            message.textContent = "";
        });
    }
})

checkReg.addEventListener('click', function (event) {
    if (this.checked) {
        formReg.removeAttribute('hidden');
        checkReg.setAttribute('disabled', 'true');
        formAuth.setAttribute('hidden', 'true');
        checkAuth.removeAttribute('disabled');
        checkAuth.checked = false;
        Array.from(document.getElementsByClassName('message')).forEach(function (message) {
            message.textContent = "";
        });
    }
})
