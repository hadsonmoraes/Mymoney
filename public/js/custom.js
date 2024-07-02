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