

    const toggleButton = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if(toggleButton && sidebar && content){
    toggleButton.addEventListener('click', function () {
        const isCollapsed = sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed', isCollapsed);
        toggleButton.setAttribute('aria-expanded', String(!isCollapsed));

        const sidebarOpen = !isCollapsed;

        fetch('/user/sidebar-toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                sidebar_open: sidebarOpen
            })
        }).then(response => {
            if (!response.ok) {
                console.error('Erro ao salvar o estado do sidebar');
            }
        }).catch(error => {
            console.error('Erro na requisição:', error);
        });
    });
    }

window.confirmarExclusao = function (event, contaId) {

    event.preventDefault();

    Swal.fire({
        title: 'Tem certeza?',
        text: 'Você não poderá reverter isso!',
        icon: 'warning',
        theme: localStorage.getItem('theme'),
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
const toggleThemeButton = document.getElementById('toggleTheme');
if (toggleThemeButton) {
    toggleThemeButton.addEventListener('click', () => {
    const current = document.body.classList.contains('theme-dark') ? 'theme-dark' : 'theme-light';
    const next = current === 'theme-dark' ? 'theme-light' : 'theme-dark';

    document.body.classList.remove(current);
    document.body.classList.add(next);

        fetch('/user/dark-mode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ theme: next })

        }).then(response => {
            if (!response.ok) {
                console.error('Erro ao salvar o tema');
            }
        }).catch(error => {
            console.error('Erro ao salvar tema:', error);
        });
        updateThemeIcon(next);
    });
}

    function applySavedTheme() {
        const savedTheme = document.body.dataset.theme || 'theme-light';
            document.body.classList.remove('theme-light', 'theme-dark');
            document.body.classList.add(savedTheme);
        updateThemeIcon(savedTheme);
    }

    function updateThemeIcon(theme) {
        const icon = document.getElementById('themeIcon');
        const text = document.getElementById('themeText');
        if (!icon) return;

        if (theme === 'theme-dark') {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            text.innerText = ' Modo Escuro';
             document.body.setAttribute('data-bs-theme', 'dark')
        } else {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            text.innerText = ' Modo Claro';
             document.body.setAttribute('data-bs-theme', 'light')
        }
    }

    applySavedTheme();
