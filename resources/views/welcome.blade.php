@extends('layouts.app')

@section('content')
<div class="w-100 h-100 d-flex align-items-center flex-column justify-content-center">
    <h1 class="display-1 font-weight-light">
        {{ __('digiserv.title') }}
    </h1>
    <div class="w-100 d-flex align-items-center justify-content-center mt-3">
        <a class="btn btn-light text-uppercase" href="{{ route('login') }}">{{ __('auth.login') }}</a>
        @if (Route::has('register'))
            <a class="btn btn-light text-uppercase ml-5" href="{{ route('register') }}">{{ __('auth.register') }}</a>
        @endif
    </div>
    <div class="w-100 d-flex align-items-center justify-content-center mt-5">
        <p class="m-0">
            {{ __('digiserv.validate_pre_text') }}
        </p>
        <a class="btn btn-secondary ml-2" href="{{ route('validate') }}">{{ __('digiserv.validate') }}</a>
    </div>

    <div class="fixed-top d-flex align-items-center justify-content-between p-3">
        <div class="btn-group">
            <a class="btn btn-secondary" href="{{ route('validate') }}">{{ __('digiserv.validate') }}</a>
        </div>
        <div class="btn-group">
            <a class="btn btn-light" href="{{ route('login') }}">{{ __('auth.login') }}</a>
            @if (Route::has('register'))
                <a class="btn btn-light" href="{{ route('register') }}">{{ __('auth.register') }}</a>
            @endif
        </div>
    </div>
</div>
@endsection
