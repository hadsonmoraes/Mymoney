

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

// 3. Modal de Exclusão de Sequência
window.abrirModalExclusaoSequencia = function (contaId, contaName, label, isRecurring) {
    const form = document.getElementById('formExcluirSequencia');
    const labelBadge = document.getElementById('modalExcluirSequenciaLabelBadge');
    const recurrenceWrapper = document.getElementById('cancelRecurrenceCheckboxWrapper');

    if (form) {
        form.action = `/contas/delete/${contaId}`;
    }

    if (labelBadge) {
        labelBadge.textContent = label || 'Repetido';
    }

    if (recurrenceWrapper) {
        if (isRecurring) {
            recurrenceWrapper.classList.remove('d-none');
        } else {
            recurrenceWrapper.classList.add('d-none');
        }
    }

    const modalEl = document.getElementById('modalExcluirSequencia');
    if (modalEl) {
        if (window.bootstrap?.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else if (window.jQuery && typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('show');
        }
    }
};

// 4. Modal Configurar Parcelamento
window.abrirModalConfigParcelamento = function (contaId, contaName, isInstallment, number, total) {
    const form = document.getElementById('formConfigurarParcelamento');
    const nameEl = document.getElementById('configParcelaContaName');
    const selectIsInstallment = document.getElementById('config_is_installment');
    const inputNumber = document.getElementById('config_installment_number');
    const inputTotal = document.getElementById('config_installments_total');
    const fieldsDiv = document.getElementById('configParcelamentoFields');

    if (form) {
        form.action = `/contas/${contaId}/configurar-parcelamento`;
    }
    if (nameEl) {
        nameEl.textContent = contaName;
    }
    if (selectIsInstallment) {
        selectIsInstallment.value = isInstallment ? '1' : '0';
        if (fieldsDiv) {
            fieldsDiv.style.display = isInstallment ? 'block' : 'none';
        }
        selectIsInstallment.onchange = function () {
            if (fieldsDiv) {
                fieldsDiv.style.display = this.value === '1' ? 'block' : 'none';
            }
        };
    }
    if (inputNumber) {
        inputNumber.value = number || 1;
    }
    if (inputTotal) {
        inputTotal.value = total || 12;
    }

    const modalEl = document.getElementById('modalConfigurarParcelamento');
    if (modalEl) {
        if (window.bootstrap?.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else if (window.jQuery && typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('show');
        }
    }
};

// 5. Carregar e Exibir Detalhes do Parcelamento
window.carregarDetalhesParcelamento = function (contaId) {
    const modalEl = document.getElementById('modalDetalhesParcelamento');
    if (!modalEl) return;

    fetch(`/contas/${contaId}/parcelamento-detalhes`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            const formatMoney = val => 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            document.getElementById('detalhesParcelaTitle').innerHTML = `<i class="fa-solid fa-layer-group text-primary me-2"></i>Parcelamento: ${data.name}`;
            document.getElementById('detalhesParcelaSubtitle').textContent = `Categoria: ${data.category} | Tipo: ${data.type}`;

            document.getElementById('detalhesProgressoLabel').textContent = `Progresso: ${data.paid_count} de ${data.total_installments} pagas`;
            document.getElementById('detalhesProgressoPercent').textContent = `${data.progress_percent}%`;
            const pBar = document.getElementById('detalhesProgressBar');
            pBar.style.width = `${data.progress_percent}%`;
            pBar.setAttribute('aria-valuenow', data.progress_percent);

            document.getElementById('detalhesValorTotal').textContent = formatMoney(data.total_value);
            document.getElementById('detalhesValorPago').textContent = formatMoney(data.paid_value);
            document.getElementById('detalhesValorRestante').textContent = formatMoney(data.remaining_value);

            document.getElementById('badgeTotalParcelas').textContent = `Total: ${data.total_installments}`;
            document.getElementById('badgePagas').textContent = `Pagas: ${data.paid_count}`;
            document.getElementById('badgePendentes').textContent = `Pendentes: ${data.pending_count}`;
            document.getElementById('badgeVencidas').textContent = `Vencidas: ${data.overdue_count}`;

            const tbody = document.getElementById('detalhesParcelasTbody');
            tbody.innerHTML = '';

            if (data.items && data.items.length) {
                data.items.forEach(item => {
                    const dateObj = new Date(item.maturity);
                    const dateFormatted = isNaN(dateObj) ? item.maturity : new Intl.DateTimeFormat('pt-BR', { timeZone: 'UTC' }).format(dateObj);
                    const tr = document.createElement('tr');
                    const sitBadge = item.situation === 'paid' ? '<span class="badge bg-success">Pago</span>' :
                        (item.situation === 'pending' ? '<span class="badge bg-warning text-dark">Pendente</span>' : '<span class="badge bg-danger">Cancelado</span>');
                    tr.innerHTML = `
                    <td class="fw-bold">${item.installment_number || '-'}/${item.installments_total || '-'}</td>
                    <td>${dateFormatted}</td>
                    <td class="fw-semibold">${formatMoney(item.value)}</td>
                    <td>${sitBadge}</td>
                `;
                    tbody.appendChild(tr);
                });
            }

            if (window.bootstrap?.Modal) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else if (window.jQuery && typeof $(modalEl).modal === 'function') {
                $(modalEl).modal('show');
            }
        })
        .catch(err => {
            console.error('Erro ao carregar detalhes do parcelamento:', err);
            alert('Não foi possível carregar os detalhes do parcelamento.');
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
