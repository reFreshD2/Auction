const formReg = document.getElementById('signUp');
const formAuth = document.getElementById('signIn');
const checkReg = document.getElementById('reg');
const checkAuth = document.getElementById('auth');

document.getElementById('auth').addEventListener('click', function (event) {
    if (this.checked) {
        formAuth.removeAttribute('hidden');
        checkAuth.setAttribute('disabled', 'true');
        formReg.setAttribute('hidden', 'true');
        checkReg.removeAttribute('disabled');
        checkReg.checked = false;
    }
})

document.getElementById('reg').addEventListener('click', function (event) {
    if (this.checked) {
        formReg.removeAttribute('hidden');
        checkReg.setAttribute('disabled', 'true');
        formAuth.setAttribute('hidden', 'true');
        checkAuth.removeAttribute('disabled');
        checkAuth.checked = false;
    }
})
