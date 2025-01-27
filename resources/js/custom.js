


window.confirmarExclusao = function (event, contaId) {

    event.preventDefault();

    Swal.fire({
        title: 'Tem certeza?',
        text: 'Você não poderá reverter isso!',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#0d6efd',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sim, excluir!',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`formExcluir${contaId}`).submit();
        }
    })

}

window.togglePassword = function (fieldId, toggleIcon) {
    const field = document.getElementById(fieldId);
    const icon = toggleIcon.querySelector('i');

    if (field.type == "password") {
        field.type = "text";
        icon.classList.remove('fa-regular', 'fa-eye')
        icon.classList.add('fa-regular', 'fa-eye-slash')
    } else {
        field.type = "password";
        icon.classList.remove('fa-regular', 'fa-eye-slash')
        icon.classList.add('fa-regular', 'fa-eye')
    }
}

let inputValor = document.getElementById('value');
if (inputValor) {
    if (inputValor.value == "") {
        inputValor.value = '0,00';
    }

    inputValor.addEventListener('input', function () {

        let valueValor = this.value.replace(/[^\d]/g, '');

        var formattedValor = (valueValor.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.')) + '' + valueValor.slice(-2);

        formattedValor = formattedValor.slice(0, -2) + ',' + formattedValor.slice(-2);

        this.value = formattedValor;

    });
}

$(function () {
    $('.select2').select2({
        theme: 'bootstrap-5'
    });
});
