function confirmarExclusao(event, contaId) {

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


let inputValor = document.getElementById('value');

if (inputValor.value == "") {
  inputValor.value = '0,00';
}

inputValor.addEventListener('input', function () {

  let valueValor = this.value.replace(/[^\d]/g, '');

  var formattedValor = (valueValor.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.')) + '' + valueValor.slice(-2);

  formattedValor = formattedValor.slice(0, -2) + ',' + formattedValor.slice(-2);

  this.value = formattedValor;

});