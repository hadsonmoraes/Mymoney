import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

function currency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value ?? 0));
}

function StatCard({ label, value, accent = 'gray' }) {
    const accents = {
        gray: 'border-gray-200 bg-white text-gray-900',
        green: 'border-green-200 bg-green-50 text-green-900',
        yellow: 'border-yellow-200 bg-yellow-50 text-yellow-900',
        red: 'border-red-200 bg-red-50 text-red-900',
        blue: 'border-blue-200 bg-blue-50 text-blue-900',
    };

    return (
        <div className={`rounded-xl border p-5 shadow-sm ${accents[accent]}`}>
            <p className="text-sm font-medium uppercase tracking-wide opacity-70">
                {label}
            </p>
            <p className="mt-2 text-2xl font-semibold">{value}</p>
        </div>
    );
}

export default function Dashboard(props) {
    const {
        contasPagasValor,
        contasPagasQuantidade,
        contasPendentesValor,
        contasPendentesQuantidade,
        contasCanceladasValor,
        contasCanceladasQuantidade,
        contasEntradaValor,
        contasEntradaQuantidade,
        contasSaidaValor,
        contasSaidaQuantidade,
        MyTotal,
        total,
        totalquantidade,
        data_inicio,
        data_fim,
    } = props;

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col gap-1">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Dashboard
                    </h2>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                        Resumo financeiro do período selecionado
                    </p>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-10">
                <div className="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                    <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <form method="get" action={route('dashboard')} className="grid gap-4 md:grid-cols-3 xl:grid-cols-5">
                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700">
                                Data início
                                <input
                                    type="date"
                                    name="data_inicio"
                                    defaultValue={data_inicio}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700">
                                Data fim
                                <input
                                    type="date"
                                    name="data_fim"
                                    defaultValue={data_fim}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </label>

                            <div className="flex items-end gap-3 md:col-span-1 xl:col-span-3">
                                <button
                                    type="submit"
                                    className="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                >
                                    Filtrar
                                </button>

                                <Link
                                    href={route('dashboard')}
                                    className="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                >
                                    Limpar
                                </Link>

                                <Link
                                    href={route('contas.create')}
                                    className="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                                >
                                    Nova conta
                                </Link>
                            </div>
                        </form>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <StatCard label="Total geral" value={currency(total)} accent="gray" />
                        <StatCard label="Saldo" value={currency(MyTotal)} accent="blue" />
                        <StatCard label="Contas pagas" value={`${contasPagasQuantidade} · ${currency(contasPagasValor)}`} accent="green" />
                        <StatCard label="Contas pendentes" value={`${contasPendentesQuantidade} · ${currency(contasPendentesValor)}`} accent="yellow" />
                        <StatCard label="Contas canceladas" value={`${contasCanceladasQuantidade} · ${currency(contasCanceladasValor)}`} accent="red" />
                        <StatCard label="Entradas" value={`${contasEntradaQuantidade} · ${currency(contasEntradaValor)}`} accent="green" />
                        <StatCard label="Saídas" value={`${contasSaidaQuantidade} · ${currency(contasSaidaValor)}`} accent="red" />
                        <StatCard label="Quantidade de contas" value={totalquantidade} accent="gray" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
