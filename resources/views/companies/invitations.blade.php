@extends('layouts.app')

@section('title', 'Коды приглашения')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-secondary-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-secondary-900 dark:text-white">Коды приглашения</h2>
                    <form action="{{ route('companies.invitations.create') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                            Создать новый код
                        </button>
                    </form>
                </div>

                @if(session('success'))
                <div class="rounded-md bg-green-50 dark:bg-green-900 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($errors->has('error'))
                <div class="rounded-md bg-red-50 dark:bg-red-900 p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $errors->first('error') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-4">
                    @if(count($invitations) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-secondary-200 dark:divide-secondary-700">
                            <thead class="bg-secondary-50 dark:bg-secondary-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 dark:text-secondary-300 uppercase tracking-wider">Код</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 dark:text-secondary-300 uppercase tracking-wider">Создан</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 dark:text-secondary-300 uppercase tracking-wider">Статус</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-secondary-800 divide-y divide-secondary-200 dark:divide-secondary-700">
                                @foreach($invitations as $invitation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg font-medium text-secondary-900 dark:text-white">{{ $invitation->code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-secondary-600 dark:text-secondary-400">{{ $invitation->created_at->format('d.m.Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Активен
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-secondary-500 dark:text-secondary-400">У вас пока нет активных кодов приглашения.</p>
                        <p class="text-secondary-500 dark:text-secondary-400 mt-2">Создайте новый код, чтобы пригласить пользователей в вашу компанию.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
