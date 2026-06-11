import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';

function currency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value ?? 0));
}

function situationLabel(value) {
    const labels = {
        paid: 'Pago',
        pending: 'Pendente',
        canceled: 'Cancelado',
    };

    return labels[value] ?? value;
}

function typeLabel(value) {
    return value === 'entrada' ? 'Entrada' : 'Saída';
}

function formatDate(value) {
    if (!value) {
        return '-';
    }

    return value.split('-').reverse().join('/');
}

export default function Show({ conta }) {
    const userId = usePage().props.auth.user?.id;

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            Conta
                        </h2>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Visualização detalhada da movimentação.
                        </p>
                    </div>

                    <Link
                        href={route('home')}
                        className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Voltar
                    </Link>
                </div>
            }
        >
            <Head title={conta.name} />

            <div className="py-10">
                <div className="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div className="grid gap-6 p-6 md:grid-cols-2">
                            <div className="space-y-4">
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Nome</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">{conta.name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Valor</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">{currency(conta.value)}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Vencimento</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {formatDate(conta.maturity)}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Situação</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">{situationLabel(conta.situation)}</p>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Categoria</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {conta.category?.name ?? '-'}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Tipo</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">{typeLabel(conta.type)}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Fixa</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">{conta.fixed ? 'Sim' : 'Não'}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Repetições</p>
                                    <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">{conta.repeat ?? 0}</p>
                                </div>
                            </div>
                        </div>

                        {conta.note && (
                            <div className="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400">Observação</p>
                                <p className="mt-2 whitespace-pre-line text-gray-900 dark:text-gray-100">{conta.note}</p>
                            </div>
                        )}

                        {conta.image && (
                            <div className="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400">Comprovante</p>
                                <img
                                    src={`/img/comprovantes${userId}/${conta.image}`}
                                    alt={conta.name}
                                    className="mt-2 max-h-96 rounded-lg border border-gray-200 object-contain dark:border-gray-700"
                                />
                            </div>
                        )}

                        <div className="flex flex-wrap gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                            <Link
                                href={route('contas.edit', conta.id)}
                                className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                            >
                                Editar
                            </Link>
                            <Link
                                href={route('situacao.alterar', conta.id)}
                                className="rounded-lg border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/30"
                            >
                                Alterar situação
                            </Link>
                            <Link
                                href={route('contas.destroy', conta.id)}
                                method="delete"
                                as="button"
                                className="rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-700 dark:text-red-300 dark:hover:bg-red-900/30"
                            >
                                Excluir
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
