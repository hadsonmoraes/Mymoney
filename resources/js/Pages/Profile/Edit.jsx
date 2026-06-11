import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Edit({ profile }) {
    const [sidebarOpen, setSidebarOpen] = useState(Boolean(profile.sidebar));
    const [theme, setTheme] = useState(profile.darkmode ?? 'theme-light');

    const { data, setData, put, processing, errors } = useForm({
        id: profile.id,
        name: profile.name,
        email: profile.email,
        password: '',
        password_confirmation: '',
    });

    const submit = (event) => {
        event.preventDefault();

        put(route('profile.update', profile.id));
    };

    const toggleSidebar = async () => {
        const next = !sidebarOpen;
        setSidebarOpen(next);

        await fetch(route('usuario.sidebar.toggle'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                Accept: 'application/json',
            },
            body: JSON.stringify({ sidebar_open: next }),
        });
    };

    const toggleTheme = async () => {
        const next = theme === 'theme-dark' ? 'theme-light' : 'theme-dark';
        setTheme(next);

        await fetch(route('usuario.darkmode'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                Accept: 'application/json',
            },
            body: JSON.stringify({ theme: next }),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            Perfil
                        </h2>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Atualize seus dados e preferências do painel.
                        </p>
                    </div>

                    <Link
                        href={route('dashboard')}
                        className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Voltar
                    </Link>
                </div>
            }
        >
            <Head title="Perfil" />

            <div className="py-10">
                <div className="mx-auto grid max-w-5xl gap-6 px-4 sm:px-6 lg:px-8 xl:grid-cols-2">
                    <form
                        onSubmit={submit}
                        className="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Dados do perfil
                            </h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Nome, e-mail e senha.
                            </p>
                        </div>

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
                            E-mail
                            <input
                                type="email"
                                value={data.email}
                                onChange={(event) => setData('email', event.target.value)}
                                className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            />
                            {errors.email && <span className="text-xs text-red-600">{errors.email}</span>}
                        </label>

                        <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Nova senha
                            <input
                                type="password"
                                value={data.password}
                                onChange={(event) => setData('password', event.target.value)}
                                className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            />
                            {errors.password && <span className="text-xs text-red-600">{errors.password}</span>}
                        </label>

                        <label className="flex flex-col gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Confirmar senha
                            <input
                                type="password"
                                value={data.password_confirmation}
                                onChange={(event) => setData('password_confirmation', event.target.value)}
                                className="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            />
                            {errors.password_confirmation && <span className="text-xs text-red-600">{errors.password_confirmation}</span>}
                        </label>

                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            Salvar
                        </button>
                    </form>

                    <div className="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Preferências
                            </h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Os toggles abaixo continuam salvando no banco.
                            </p>
                        </div>

                        <div className="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 dark:border-gray-700">
                            <div>
                                <p className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Menu lateral
                                </p>
                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                    {sidebarOpen ? 'Aberto' : 'Fechado'}
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={toggleSidebar}
                                className="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Alternar
                            </button>
                        </div>

                        <div className="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 dark:border-gray-700">
                            <div>
                                <p className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Tema
                                </p>
                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                    {theme === 'theme-dark' ? 'Escuro' : 'Claro'}
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={toggleTheme}
                                className="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Alternar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
