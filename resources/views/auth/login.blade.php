@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="flex items-center h-screen w-full">
        <div class="w-full bg-white rounded shadow-lg p-8 m-4 mt-24 md:max-w-sm md:mx-auto">
            <p class="lock w-full text-xl text-center uppercase font-bold mb-4">{{ __('Login') }}</p>
            <form class="mb-4" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4 md:w-full">
                    <label for="email" class="block text-xs mb-1">Email</label>
                    <input class="w-full border rounded p-2 outline-none focus:shadow-outline" type="email" name="email" id="email" />
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                         </span>
                    @enderror
                </div>
                <div class="mb-4 md:w-full">
                    <label for="password" class="block text-xs mb-1">Password</label>
                    <input class="w-full border rounded p-2 outline-none focus:shadow-outline" type="password" name="password" id="password" />
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                         </span>
                    @enderror
                </div>
                <div class="mb-4">
                      <input id="remember" type="checkbox" name="remember" class="form-checkbox" {{ old('remember') ? 'checked' : '' }}>
                      <label for="remember" class="ml-2">{{ __('Remember Me') }}</label>
                </div>                  
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('register') }}" class="bg-gray-400 hover:bg-gray-700 text-white uppercase text-sm font-semibold px-4 py-2 rounded">
                        Register
                    </a>
                    <button type="sumbit" class="bg-blue-500 hover:bg-blue-700 text-white uppercase text-sm font-semibold px-4 py-2 rounded">
                        {{ __('Login') }}
                    </button>
                </div>
                <div class="flex justify-center mt-4">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                             {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection