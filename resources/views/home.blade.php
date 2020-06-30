@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">{{ __('digiserv.home_title') }}</h4></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="alert alert-info mb-0" role="alert">
                        <h4><i class="fas fa-info-circle"></i>&nbsp;{{ __('digiserv.note') }}</h4>
                        <p class="mb-0">
                            {!! __('digiserv.home_text') !!}
                        </p>
                    </div>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary btn-lg" href="{{ route('transform') }}"><i class="far fa-play-circle"></i>&nbsp;{{ __('digiserv.start_transform') }}</a>
                    <a class="btn btn-primary float-right btn-lg" href="{{ route('validate') }}"><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.validate') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
