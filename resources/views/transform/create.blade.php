@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('digiserv.transform') }}</h4>
                    <div class="progress h-auto">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%; height: 20px;" 
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">{{ __('digiserv.transform_progress_step_3') }}</div>
                    </div>
                </div>
                <div class="card-body">
                    @if(sizeof($elmoElements) > 0)
                        
                        @if($xmlError)
                            <div class="alert alert-danger" role="alert">
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                                <p>
                                    {{ __('digiserv.transform_elmo_invalid_xml') }}
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
                                            @foreach($elmoElements as $elmoElement)
                                                @if(!$elmoElement->isXmlValid())
                                                    <h6><b>{{ $elmoElement->getBasenameUnsigned() }}:</b></h6>
                                                    <p>
                                                        @foreach($elmoElements->getXmlErrors() as $error)
                                                            {!! $error !!}
                                                            @if(!$loop->last)
                                                                <hr>
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success" role="alert">
                                <h4><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.success') }}</h4>
                                <p class="mb-0">{{ __('digiserv.transform_elmo_valid_xml') }}</p>
                            </div>
                        @endif

                        @if($signatureError)
                            <div class="alert alert-danger" role="alert">
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                                <p>
                                    {{ __('digiserv.transform_elmo_invalid_signature') }}
                                </p>
                                <div class="card">
                                    <div class="card-header" id="heading-signatureErrors">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed p-0 text-danger" data-toggle="collapse" 
                                                data-target="#collapse-signatureErrors" aria-expanded="false" aria-controls="collapse-signatureErrors">
                                                <i class="fas fa-chevron-right mr-2" aria-hidden="true"></i>{{ __('digiserv.show_error') }}
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-signatureErrors" class="collapse" aria-labelledby="heading-signatureErrors">
                                        <div class="card-body text-danger">
                                            <ul class="list-group">
                                                @foreach($elmoElements as $elmoElement)
                                                    @if(!$elmoElement->isSignatureValid())
                                                        <li class="list-group-item">{{ $elmoElement->getBasenameSigned() }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success" role="alert">
                                <h4><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.success') }}</h4>
                                <p class="mb-0">{{ __('digiserv.transform_elmo_valid_signature') }}</p>
                            </div>
                        @endif
                        
                        @if($xmlSchemaError)
                            <div class="alert alert-danger" role="alert">
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                                <p>
                                    {{ __('digiserv.transform_elmo_invalid_xml_schema') }}
                                </p>
                                <div class="card">
                                    <div class="card-header" id="heading-xmlSchemaErrors">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed p-0 text-danger" data-toggle="collapse" 
                                                data-target="#collapse-xmlSchemaErrors" aria-expanded="false" aria-controls="collapse-xmlSchemaErrors">
                                                <i class="fas fa-chevron-right mr-2" aria-hidden="true"></i>Fehler anzeigen
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse-xmlSchemaErrors" class="collapse" aria-labelledby="heading-xmlSchemaErrors">
                                        <div class="card-body text-danger">
                                            <ul class="list-group">
                                                @foreach($elmoElements as $elmoElement)
                                                    @if(!$elmoElement->isXmlSchemaValid())
                                                        <li class="list-group-item">
                                                            <h6><b>{{ $elmoElement->getBasenameSigned() }}:</b></h6>
                                                            <p>
                                                                @foreach($elmoElement->getXmlSchemaErrors() as $error)
                                                                    {!! $error !!}
                                                                    @if(!$loop->last)
                                                                        <hr>
                                                                    @endif
                                                                @endforeach
                                                            </p>    
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success" role="alert">
                                <h4><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.success') }}</h4>
                                <p class="mb-0">{{ __('digiserv.transform_elmo_valid_xml_schema') }}</p>
                            </div>
                        @endif
                        
                        @if(!$xmlError && !$signatureError && !$xmlSchemaError && $zip_path != "" && $zip_filename != "")
                            <div class="alert alert-success mb-0" role="alert">
                                <h4><i class="fas fa-file-download"></i>&nbsp;{{ __('digiserv.download') }}</h4>
                                <p class="mb-0">
                                    {{ __('digiserv.transform_download_text') }}
                                </p>
                                <div class="w-100 text-center pt-2">
                                    <form id="transformDownloadForm" action="{{ route('transform/download') }}" method="post">
                                        <button id="transformDownloadButton" type="button" class="btn btn-success btn-lg" data-zippath="{{ $zip_path }}">
                                            <i class="far fa-file-archive"></i>&nbsp;&laquo;{{ $zip_filename }}&raquo;
                                        </button>
                                     </form>
                                </div>
                            </div>
                        @elseif($zip_filename == "" || $zip_path == "")
                            error creating zip
                        @endif
                    @else
                        <div class="alert alert-danger mb-0" role="alert">
                            <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                            <p class="mb-0">
                                {{ __('digiserv.transform_modules_empty_text') }}<a href="{{ route('transform') }}" 
                                    title="{{ __('digiserv.start_transform') }}" class="alert-link">{{ __('digiserv.start_transform') }}</a>
                            </p>
                        </div>
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('transform') }}" class="btn btn-danger">
                        <i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.cancel_button_text') }}
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-success">
                        <i class="fas fa-check"></i>&nbsp;{{ __('digiserv.finish') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
