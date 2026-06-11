import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(value ?? 0));
}

export default function Form({ conta, categorys, mode }) {
    const isEdit = mode === 'edit';

    const { data, setData, post, put, processing, errors } = useForm({
        id: conta?.id ?? '',
        name: conta?.name ?? '',
        value: conta ? formatCurrency(conta.value) : '0,00',
        maturity: conta?.maturity ?? '',
        situation: conta?.situation ?? 'pending',
        category_id: conta?.category_id ?? '',
        type: conta?.type ?? 'saida',
        fixed: conta?.fixed ?? false,
        repeat: conta?.repeat ?? 0,
        note: conta?.note ?? '',
        image: null,
    });

    const submit = (event) => {
        event.preventDefault();

        const options = { forceFormData: true };

        if (isEdit) {
            put(route('contas.update', conta.id), options);
            return;
        }

        post(route('contas.store'), options);
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            {isEdit ? 'Editar conta' : 'Nova conta'}
                        </h2>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Preencha os dados financeiros da conta.
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
            <Head title={isEdit ? 'Editar conta' : 'Nova conta'} />

            <div className="py-10">
                <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div className="grid gap-4 md:grid-cols-2">
                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Nome
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(event) => setData('name', event.target.value)}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                                {errors.name && <span className="text-xs text-red-600">{errors.name}</span>}
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Valor
                                <input
                                    type="text"
                                    value={data.value}
                                    onChange={(event) => setData('value', event.target.value)}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                                {errors.value && <span className="text-xs text-red-600">{errors.value}</span>}
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Vencimento
                                <input
                                    type="date"
                                    value={data.maturity}
                                    onChange={(event) => setData('maturity', event.target.value)}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                                {errors.maturity && <span className="text-xs text-red-600">{errors.maturity}</span>}
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Categoria
                                <select
                                    value={data.category_id}
                                    onChange={(event) => setData('category_id', event.target.value)}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="">Selecione</option>
                                    {categorys.map((category) => (
                                        <option key={category.id} value={category.id}>
                                            {category.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.category_id && <span className="text-xs text-red-600">{errors.category_id}</span>}
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Situação
                                <select
                                    value={data.situation}
                                    onChange={(event) => setData('situation', event.target.value)}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="pending">Pendente</option>
                                    <option value="paid">Pago</option>
                                    <option value="canceled">Cancelado</option>
                                </select>
                                {errors.situation && <span className="text-xs text-red-600">{errors.situation}</span>}
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Tipo
                                <select
                                    value={data.type}
                                    onChange={(event) => setData('type', event.target.value)}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="entrada">Entrada</option>
                                    <option value="saida">Saída</option>
                                </select>
                                {errors.type && <span className="text-xs text-red-600">{errors.type}</span>}
                            </label>
                        </div>

                        <div className="grid gap-4 md:grid-cols-3">
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                <input
                                    type="checkbox"
                                    checked={Boolean(data.fixed)}
                                    onChange={(event) => setData('fixed', event.target.checked)}
                                    className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                Fixa
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Repetições
                                <input
                                    type="number"
                                    min="0"
                                    value={data.repeat}
                                    onChange={(event) => setData('repeat', event.target.value)}
                                    className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                />
                            </label>

                            <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200 md:col-span-1">
                                Comprovante
                                <input
                                    type="file"
                                    onChange={(event) => setData('image', event.target.files[0])}
                                    className="block w-full rounded-lg border border-gray-300 text-sm shadow-sm file:mr-4 file:rounded-l-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700 dark:border-gray-600 dark:text-gray-200"
                                />
                            </label>
                        </div>

                        <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Observação
                            <textarea
                                value={data.note}
                                onChange={(event) => setData('note', event.target.value)}
                                rows="5"
                                className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            />
                        </label>

                        <div className="flex items-center gap-3">
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                {isEdit ? 'Salvar alterações' : 'Cadastrar'}
                            </button>
                            <Link
                                href={route('home')}
                                className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
