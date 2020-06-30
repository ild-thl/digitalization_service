@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">{{ __('digiserv.validate_result') }}</h4></div>
                <div class="card-body">
                    @if(sizeof($xmlErrors) == 0)
                        <div class="alert alert-success" role="alert">
                            <h4><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.success') }}</h4>
                            <p class="mb-0">{{ __('digiserv.validate_valid_xml') }}</p>
                        </div>
                    @else
                        <div class="alert alert-danger" role="alert">
                            <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                            <p>
                                {{ __('digiserv.validate_invalid_xml') }}
                            </p>
                            <div class="card">
                                <div class="card-header" id="heading-xmlErrors">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed p-0 text-danger" data-toggle="collapse" data-target="#collapse-xmlErrors" 
                                            aria-expanded="false" aria-controls="collapse-xmlErrors">
                                            <i class="fas fa-chevron-right mr-2" aria-hidden="true"></i>{{ __('digiserv.show_error') }}
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse-xmlErrors" class="collapse" aria-labelledby="heading-xmlErrors">
                                    <div class="card-body text-danger">
                                        <p>
                                            @foreach($xmlErrors as $error)
                                                {!! $error !!}
                                                @if(!$loop->last)
                                                    <hr>
                                                @endif
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(sizeof($xmlSchemaErrors) == 0 && sizeof($xmlErrors) == 0)
                        <div class="alert alert-success" role="alert">
                            <h4><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.success') }}</h4>
                            <p class="mb-0">{{ __('digiserv.validate_valid_schema') }}</p>
                        </div>
                    @elseif(sizeof($xmlSchemaErrors) > 0 && sizeof($xmlErrors) == 0)
                        <div class="alert alert-danger" role="alert">
                            <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                            <p>
                                {{ __('digiserv.validate_invalid_schema') }}
                            </p>
                            <div class="card">
                                <div class="card-header" id="heading-xmlSchemaErrors">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed p-0 text-danger" data-toggle="collapse" data-target="#collapse-xmlSchemaErrors" 
                                            aria-expanded="false" aria-controls="collapse-xmlSchemaErrors">
                                            <i class="fas fa-chevron-right mr-2" aria-hidden="true"></i>{{ __('digiserv.show_error') }}
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse-xmlSchemaErrors" class="collapse" aria-labelledby="heading-xmlSchemaErrors">
                                    <div class="card-body text-danger">
                                        <p>
                                            @foreach($xmlSchemaErrors as $error)
                                                {!! $error !!}
                                                @if(!$loop->last)
                                                    <hr>
                                                @endif
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(sizeof($xmlSchemaErrors) == 0 && sizeof($xmlErrors) == 0 && $signatureValid)
                        <div class="alert alert-success mb-0" role="alert">
                            <h4><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.success') }}</h4>
                            <p class="mb-0">{{ __('digiserv.validate_valid_signature') }}</p>
                        </div>
                    @elseif(sizeof($xmlSchemaErrors) == 0 && sizeof($xmlErrors) == 0)
                        <div class="alert alert-danger mb-0" role="alert">
                            <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                            <p class="mb-0">{{ __('digiserv.validate_invalid_signature') }}</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('validate') }}" class="btn btn-secondary">
                        <i class="fas fa-redo-alt"></i>&nbsp;{{ __('digiserv.validate_redo') }}
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-success float-right">
                        <i class="fas fa-check"></i>&nbsp;{{ __('digiserv.finish') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
