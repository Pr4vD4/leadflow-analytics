@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-secondary-900">
    <div class="w-full max-w-md space-y-8" data-aos="fade-up" data-aos-duration="800">
        <div>
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-secondary-900 dark:text-white">
                Регистрация компании
            </h2>
            <p class="mt-2 text-center text-sm text-secondary-600 dark:text-secondary-300">
                Уже зарегистрированы?
                <a href="{{ route('login') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300">
                    Войдите в аккаунт
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Название компании</label>
                    <input id="company_name" name="company_name" type="text" required value="{{ old('company_name') }}"
                           class="relative block w-full rounded-md border-0 py-2.5 px-3.5 bg-white dark:bg-secondary-800 text-secondary-900 dark:text-white placeholder:text-secondary-400 dark:placeholder:text-secondary-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:focus:ring-primary-500 sm:text-sm sm:leading-6 @error('company_name') ring-red-500 focus:ring-red-500 @enderror"
                           placeholder="Название компании">
                    @error('company_name')
                    <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Ваше имя</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                           class="relative block w-full rounded-md border-0 py-2.5 px-3.5 bg-white dark:bg-secondary-800 text-secondary-900 dark:text-white placeholder:text-secondary-400 dark:placeholder:text-secondary-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:focus:ring-primary-500 sm:text-sm sm:leading-6 @error('name') ring-red-500 focus:ring-red-500 @enderror"
                           placeholder="Ваше имя">
                    @error('name')
                    <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="relative block w-full rounded-md border-0 py-2.5 px-3.5 bg-white dark:bg-secondary-800 text-secondary-900 dark:text-white placeholder:text-secondary-400 dark:placeholder:text-secondary-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:focus:ring-primary-500 sm:text-sm sm:leading-6 @error('email') ring-red-500 focus:ring-red-500 @enderror"
                           placeholder="Email">
                    @error('email')
                    <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Пароль</label>
                    <input id="password" name="password" type="password" required
                           class="relative block w-full rounded-md border-0 py-2.5 px-3.5 bg-white dark:bg-secondary-800 text-secondary-900 dark:text-white placeholder:text-secondary-400 dark:placeholder:text-secondary-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:focus:ring-primary-500 sm:text-sm sm:leading-6 @error('password') ring-red-500 focus:ring-red-500 @enderror"
                           placeholder="Пароль">
                    @error('password')
                    <p class="mt-1 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Подтверждение пароля</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="relative block w-full rounded-md border-0 py-2.5 px-3.5 bg-white dark:bg-secondary-800 text-secondary-900 dark:text-white placeholder:text-secondary-400 dark:placeholder:text-secondary-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:focus:ring-primary-500 sm:text-sm sm:leading-6"
                           placeholder="Подтверждение пароля">
                </div>
            </div>

            @if($errors->has('error'))
                <div class="rounded-md bg-red-50 dark:bg-red-900 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $errors->first('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <button type="submit" class="group relative flex w-full justify-center rounded-md bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:focus-visible:outline-primary-400">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-primary-500 group-hover:text-primary-400 dark:text-primary-400 dark:group-hover:text-primary-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Зарегистрироваться
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
