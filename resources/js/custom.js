

const toggleButton = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');

if (toggleButton && sidebar && content) {
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

// ==========================================
// Recorrência & Repetição de Lançamentos
// ==========================================

document.addEventListener('DOMContentLoaded', function () {
    // 1. Dinamismo no formulário de Criação/Edição para Recorrência
    const recurrenceSelect = document.getElementById('recurrence_type');
    const customIntervalDiv = document.getElementById('customIntervalDiv');
    const recurrenceEndDateDiv = document.getElementById('recurrenceEndDateDiv');
    const recurrenceMaxDiv = document.getElementById('recurrenceMaxDiv');

    if (recurrenceSelect) {
        function updateRecurrenceVisibility() {
            const val = recurrenceSelect.value;
            if (customIntervalDiv) {
                if (val === 'custom') {
                    customIntervalDiv.classList.remove('d-none');
                } else {
                    customIntervalDiv.classList.add('d-none');
                }
            }

            const isNone = val === 'none';
            if (recurrenceEndDateDiv) {
                if (!isNone) {
                    recurrenceEndDateDiv.classList.remove('d-none');
                } else {
                    recurrenceEndDateDiv.classList.add('d-none');
                }
            }

            if (recurrenceMaxDiv) {
                if (!isNone) {
                    recurrenceMaxDiv.classList.remove('d-none');
                } else {
                    recurrenceMaxDiv.classList.add('d-none');
                }
            }
        }

        recurrenceSelect.addEventListener('change', updateRecurrenceVisibility);
        updateRecurrenceVisibility();
    }

    // 2. Modal de Repetir Lançamento
    const repeatButtons = document.querySelectorAll('.btn-repeat');
    const formRepetir = document.getElementById('formRepetirLancamento');
    const repeatFrequencySelect = document.getElementById('repeat_frequency');
    const repeatCustomIntervalWrapper = document.getElementById('repeat_custom_interval_wrapper');

    repeatButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const value = this.dataset.value;
            const maturity = this.dataset.maturity;

            if (formRepetir) {
                formRepetir.action = `/contas/${id}/repetir`;
            }

            const nameEl = document.getElementById('modalRepeatContaName');
            const valEl = document.getElementById('modalRepeatContaValue');
            const matEl = document.getElementById('modalRepeatContaMaturity');
            const tokenEl = document.getElementById('repeat_operation_token');

            if (nameEl) nameEl.textContent = name;
            if (valEl) valEl.textContent = 'R$ ' + value;
            if (matEl) matEl.textContent = maturity;
            if (tokenEl) tokenEl.value = 'token_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
        });
    });

    if (repeatFrequencySelect && repeatCustomIntervalWrapper) {
        repeatFrequencySelect.addEventListener('change', function () {
            if (this.value === 'custom') {
                repeatCustomIntervalWrapper.classList.remove('d-none');
            } else {
                repeatCustomIntervalWrapper.classList.add('d-none');
            }
        });
    }

    // Frequência no show.blade.php se existir
    const repeatFreqShow = document.getElementById('repeat_frequency_show');
    const repeatCustomShow = document.getElementById('repeat_custom_interval_show');
    if (repeatFreqShow && repeatCustomShow) {
        repeatFreqShow.addEventListener('change', function () {
            if (this.value === 'custom') {
                repeatCustomShow.classList.remove('d-none');
            } else {
                repeatCustomShow.classList.add('d-none');
            }
        });
    }

    // Proteção contra duplo envio no modal de repetição
    if (formRepetir) {
        formRepetir.addEventListener('submit', function () {
            const btn = document.getElementById('btnSubmitRepetir');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Processando...';
            }
        });
    }
});

// 3. Exclusão de Sequência (Lançamento Repetido) via Swal.fire
window.confirmarExclusaoSequencia = function (contaId, contaName, label, isRecurring) {
    const escopoHtml = `
        <div class="text-start">
            <p class="small text-muted mb-3">
                "${contaName}" pertence a uma sequência de repetição
                (<strong>${label || 'Repetido'}</strong>). Como deseja prosseguir com a exclusão?
            </p>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="swal_delete_scope"
                    id="swal_delete_scope_only" value="only_this" checked>
                <label class="form-check-label" for="swal_delete_scope_only">
                    <strong>Excluir somente este lançamento</strong>
                    <div class="small text-muted">Apenas este registro será apagado. Os demais
                        continuam inalterados.</div>
                </label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="swal_delete_scope"
                    id="swal_delete_scope_this_and_next" value="this_and_next">
                <label class="form-check-label" for="swal_delete_scope_this_and_next">
                    <strong>Excluir este e os lançamentos posteriores</strong>
                    <div class="small text-muted">Apaga este lançamento e todas as ocorrências
                        futuras desta mesma sequência.</div>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="swal_delete_scope"
                    id="swal_delete_scope_all" value="all_sequence">
                <label class="form-check-label" for="swal_delete_scope_all">
                    <strong>Excluir toda a sequência</strong>
                    <div class="small text-muted">Apaga todos os lançamentos gerados nesta
                        repetição.</div>
                </label>
            </div>
            ${isRecurring ? `
            <div class="form-check text-start mt-3">
                <input class="form-check-input" type="checkbox" id="swal_cancel_recurrence" value="1">
                <label class="form-check-label text-warning-emphasis" for="swal_cancel_recurrence">
                    Cancelar também a regra de recorrência automática futura
                </label>
            </div>` : ''}
        </div>`;

    Swal.fire({
        title: 'Excluir lançamento repetido',
        html: escopoHtml,
        icon: 'warning',
        theme: localStorage.getItem('theme'),
        showCancelButton: true,
        cancelButtonColor: '#0d6efd',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sim, excluir!',
        preConfirm: () => {
            const scope = document.querySelector('input[name="swal_delete_scope"]:checked')?.value || 'only_this';
            const cancelRecurrence = document.getElementById('swal_cancel_recurrence')?.checked ?? false;
            return { scope, cancelRecurrence };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/contas/delete/${contaId}`;
            form.style.display = 'none';

            const campos = {
                _token: csrfToken,
                _method: 'DELETE',
                delete_scope: result.value.scope,
            };

            if (result.value.cancelRecurrence) {
                campos.cancel_recurrence = '1';
            }

            Object.entries(campos).forEach(([nome, valor]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = nome;
                input.value = valor;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    });
};

// 4. Cancelamento de Recorrência via Swal.fire
window.confirmarCancelamentoRecorrencia = function (contaId, contaName) {
    Swal.fire({
        title: 'Cancelar recorrência?',
        html: `
            <div class="text-start">
                <p class="mb-2">
                    <strong>"${contaName}"</strong>
                </p>
                <p class="small text-muted mb-1">
                    Ao cancelar a recorrência, <strong>novos lançamentos não serão gerados
                    automaticamente</strong> para este compromisso.
                </p>
                <p class="small text-muted mb-0">
                    Os lançamentos já registrados serão mantidos.
                </p>
            </div>`,
        icon: 'warning',
        theme: localStorage.getItem('theme'),
        showCancelButton: true,
        cancelButtonColor: '#0d6efd',
        cancelButtonText: 'Manter recorrência',
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'Sim, cancelar!',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/contas/${contaId}/cancelar-recorrencia`;
            form.style.display = 'none';

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]')?.content;
            form.appendChild(token);

            document.body.appendChild(form);
            form.submit();
        }
    });
};

// 6. Manipulação de Importação via Excel / CSV
document.addEventListener('DOMContentLoaded', function () {
    const btnAnalisar = document.getElementById('btnAnalisarExcel');
    const fileInput = document.getElementById('excel_file_input');
    const errorDiv = document.getElementById('excelFileError');
    const spinner = document.getElementById('excelLoadingSpinner');
    const previewContainer = document.getElementById('excelPreviewContainer');

    if (btnAnalisar && fileInput) {
        btnAnalisar.addEventListener('click', function () {
            if (!fileInput.files || !fileInput.files[0]) {
                if (errorDiv) {
                    errorDiv.textContent = 'Por favor selecione um arquivo Excel (.xlsx) ou CSV.';
                    errorDiv.classList.remove('d-none');
                }
                return;
            }

            if (errorDiv) errorDiv.classList.add('d-none');
            if (spinner) spinner.classList.remove('d-none');
            if (previewContainer) previewContainer.classList.add('d-none');
            btnAnalisar.disabled = true;

            const formData = new FormData();
            formData.append('import_file', fileInput.files[0]);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) formData.append('_token', token);

            fetch('/contas/importar/preview', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    btnAnalisar.disabled = false;
                    if (spinner) spinner.classList.add('d-none');

                    if (!res.success) {
                        if (errorDiv) {
                            errorDiv.textContent = res.message || 'Erro ao processar arquivo.';
                            errorDiv.classList.remove('d-none');
                        }
                        return;
                    }

                    const data = res.data;
                    document.getElementById('previewTotalRows').textContent = data.total_rows;
                    document.getElementById('previewValidRows').textContent = data.valid_rows;
                    document.getElementById('previewWarnings').textContent = data.warnings_count;
                    document.getElementById('previewErrors').textContent = data.errors_count;

                    // Erros
                    const errorsBox = document.getElementById('excelErrorsBox');
                    const errorsList = document.getElementById('excelErrorsList');
                    if (data.errors_count > 0 && errorsBox && errorsList) {
                        errorsList.innerHTML = '';
                        data.errors.slice(0, 10).forEach(err => {
                            const li = document.createElement('li');
                            li.textContent = `Linha ${err.row}: ${err.message}`;
                            errorsList.appendChild(li);
                        });
                        if (data.errors.length > 10) {
                            const liMore = document.createElement('li');
                            liMore.textContent = `... e mais ${data.errors.length - 10} linha(s) com erro.`;
                            errorsList.appendChild(liMore);
                        }
                        errorsBox.classList.remove('d-none');
                    } else if (errorsBox) {
                        errorsBox.classList.add('d-none');
                    }

                    // Avisos de duplicidade
                    const warningsBox = document.getElementById('excelWarningsBox');
                    const warningsList = document.getElementById('excelWarningsList');
                    if (data.warnings_count > 0 && warningsBox && warningsList) {
                        warningsList.innerHTML = '';
                        data.warnings.slice(0, 5).forEach(w => {
                            const li = document.createElement('li');
                            li.textContent = `Linha ${w.row}: ${w.message}`;
                            warningsList.appendChild(li);
                        });
                        warningsBox.classList.remove('d-none');
                    } else if (warningsBox) {
                        warningsBox.classList.add('d-none');
                    }

                    // Tabela de prévia
                    const tbody = document.getElementById('excelPreviewTableBody');
                    tbody.innerHTML = '';
                    const formatMoney = val => 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    data.rows.slice(0, 10).forEach(r => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                        <td>${r.row}</td>
                        <td class="fw-semibold">${r.name}</td>
                        <td>${r.maturity}</td>
                        <td>${formatMoney(r.value)}</td>
                        <td>${r.type === 'entrada' ? '<span class="text-success">Entrada</span>' : '<span class="text-danger">Saída</span>'}</td>
                        <td>${r.category}</td>
                        <td><span class="badge ${r.situation === 'paid' ? 'bg-success' : 'bg-warning text-dark'}">${r.situation === 'paid' ? 'Pago' : 'Pendente'}</span></td>
                    `;
                        tbody.appendChild(tr);
                    });

                    const btnConfirmar = document.getElementById('btnConfirmarImportacao');
                    if (btnConfirmar) {
                        btnConfirmar.disabled = data.valid_rows === 0;
                    }

                    if (previewContainer) previewContainer.classList.remove('d-none');
                })
                .catch(err => {
                    btnAnalisar.disabled = false;
                    if (spinner) spinner.classList.add('d-none');
                    if (errorDiv) {
                        errorDiv.textContent = 'Erro na comunicação com o servidor ao analisar planilha.';
                        errorDiv.classList.remove('d-none');
                    }
                });
        });
    }
});
