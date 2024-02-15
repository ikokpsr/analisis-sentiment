@extends('layouts.app')

@section('title', 'Register')

@section('content')
{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<div class="flex items-center h-screen w-full">
    <div class="w-full bg-white rounded shadow-lg p-8 m-4 md:max-w-sm md:mx-auto">
        <p class="lock w-full text-xl text-center uppercase font-bold mb-4">{{ __('Register') }}</p>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-4 md:w-full">
                <label for="name" class="block text-xs mb-1">Name</label>
                <input class="w-full border rounded p-2 outline-none focus:shadow-outline" type="name" name="name" id="name" required autocomplete="name" />
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                @enderror
            </div>
            <div class="mb-4 md:w-full">
                <label for="email" class="block text-xs mb-1">Email</label>
                <input class="w-full border rounded p-2 outline-none focus:shadow-outline" type="email" name="email" id="email" required autocomplete="email" />
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                @enderror
            </div>
            <div class="mb-4 md:w-full">
                <label for="password" class="block text-xs mb-1">Password</label>
                <input class="w-full border rounded p-2 outline-none focus:shadow-outline" type="password" name="password" id="password" required autocomplete="new-password" />
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                @enderror
            </div>
            <div class="mb-4 md:w-full">
                <label for="password-confirm" class="block text-xs mb-1">Confirm Password</label>
                <input class="w-full border rounded p-2 outline-none focus:shadow-outline" type="password" name="password_confirmation" id="password-confirm" required autocomplete="new-password" />
            </div>
            <div class="flex justify-center space-x-4">
                <button type="submit" class="bg-gray-400 hover:bg-gray-700 text-white uppercase text-sm font-semibold px-4 py-2 rounded">
                    {{ __('Register') }}
                </button>
            </div>
            <div class="flex justify-end mt-4">
                <a class="btn btn-link" href="{{ route('login') }}">
                    {{ __('Login') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
