import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

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

export default function Index({
    contas,
    name,
    data_inicio,
    data_fim,
    situation,
    type,
    perPage,
}) {
    const filters = {
        name,
        data_inicio,
        data_fim,
        situation,
        type,
        perPage,
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            Contas
                        </h2>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Filtre, acompanhe e exporte suas contas.
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <Link
                            href={route('contas.create')}
                            className="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            Nova conta
                        </Link>
                        <Link
                            href={route('contas.gerar-csv', filters)}
                            className="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Exportar CSV
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title="Contas" />

            <div className="py-10">
                <div className="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                    <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <form method="get" action={route('home')} className="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Nome
                                <input
                                    type="text"
                                    name="name"
                                    defaultValue={name}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Data início
                                <input
                                    type="date"
                                    name="data_inicio"
                                    defaultValue={data_inicio}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Data fim
                                <input
                                    type="date"
                                    name="data_fim"
                                    defaultValue={data_fim}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Situação
                                <select
                                    name="situation"
                                    defaultValue={situation ?? ''}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="">Todos</option>
                                    <option value="paid">Pago</option>
                                    <option value="pending">Pendente</option>
                                    <option value="canceled">Cancelado</option>
                                </select>
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Tipo
                                <select
                                    name="type"
                                    defaultValue={type ?? ''}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="">Todos</option>
                                    <option value="entrada">Entrada</option>
                                    <option value="saida">Saída</option>
                                </select>
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Por página
                                <select
                                    name="perPage"
                                    defaultValue={perPage}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    {[5, 10, 25, 50, 100, 150, 200].map((item) => (
                                        <option key={item} value={item}>
                                            {item}
                                        </option>
                                    ))}
                                </select>
                            </label>

                            <div className="flex items-end gap-3 xl:col-span-6">
                                <button
                                    type="submit"
                                    className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                >
                                    Buscar
                                </button>

                                <Link
                                    href={route('home')}
                                    className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                                >
                                    Limpar
                                </Link>
                            </div>
                        </form>
                    </div>

                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead className="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Nome
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Vencimento
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Situação
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Categoria
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Tipo
                                        </th>
                                        <th className="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Valor
                                        </th>
                                        <th className="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                                    {contas.data.length > 0 ? (
                                        contas.data.map((conta) => (
                                            <tr key={conta.id} className="hover:bg-gray-50 dark:hover:bg-gray-900">
                                                <td className="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {conta.name}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                    {formatDate(conta.maturity)}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                    {situationLabel(conta.situation)}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                    {conta.category?.name ?? '-'}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                    {typeLabel(conta.type)}
                                                </td>
                                                <td className="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {currency(conta.value)}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex justify-end gap-2">
                                                        <Link
                                                            href={route('contas.show', conta.id)}
                                                            className="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                                                        >
                                                            Ver
                                                        </Link>
                                                        <Link
                                                            href={route('contas.edit', conta.id)}
                                                            className="rounded-lg border border-indigo-300 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50 dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                                                        >
                                                            Editar
                                                        </Link>
                                                        <Link
                                                            href={route('situacao.alterar', conta.id)}
                                                            className="rounded-lg border border-amber-300 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/30"
                                                        >
                                                            Situação
                                                        </Link>
                                                        <Link
                                                            href={route('contas.destroy', conta.id)}
                                                            method="delete"
                                                            as="button"
                                                            className="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-700 dark:text-red-300 dark:hover:bg-red-900/30"
                                                        >
                                                            Excluir
                                                        </Link>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="7" className="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                                Nenhuma conta encontrada.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {contas.links.length > 3 && (
                            <div className="flex flex-wrap items-center gap-2 border-t border-gray-200 px-4 py-4 dark:border-gray-700">
                                {contas.links.map((link) => (
                                    <Link
                                        key={link.label}
                                        href={link.url || ''}
                                        preserveScroll
                                        className={`rounded-lg px-3 py-2 text-sm font-medium ${
                                            link.active
                                                ? 'bg-indigo-600 text-white'
                                                : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700'
                                        } ${!link.url ? 'pointer-events-none opacity-50' : ''}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
