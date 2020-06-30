@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <form id="transformStartForm" method="POST" action="{{ route('transform/assign') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header">
                        <h4>{{ __('digiserv.transform') }}</h4>
                        <div class="progress h-auto">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 33.33%; height: 20px;" aria-valuenow="33.3333" aria-valuemin="0" aria-valuemax="100">{{ __('digiserv.transform_progress_step_1') }}</div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label for="issuerTitle">{{ __('digiserv.transform_issuer_title') }}</label>
                            <input id="issuerTitle" type="text" class="form-control @error('issuerTitle') is-invalid @enderror" name="issuerTitle" placeholder="{{ __('digiserv.transform_issuer_title') }}" value="{{ $user->issuer_title }}" required>
                        </div>
                        <div class="form-group">
                            <label for="issuerIdentifier">{{ __('digiserv.transform_issuer_identifier') }}</label>
                            <input id="issuerIdentifier" type="text" class="form-control @error('issuerIdentifier') is-invalid @enderror" name="issuerIdentifier" placeholder="{{ __('digiserv.transform_issuer_identifier') }}" value="{{ $user->issuer_identifier }}" required>
                        </div>
                        <div class="form-group">
                            <label for="issuerUrl">{{ __('digiserv.transform_issuer_url') }}</label>
                            <input id="issuerUrl" type="url" class="form-control @error('issuerUrl') is-invalid @enderror" name="issuerUrl" placeholder="{{ __('digiserv.transform_issuer_url') }}" value="{{ $user->issuer_url }}" required>
                        </div>

                        <div class="form-group py-4 mb-0">
                            @error('xmlFile')
                            <div id="xmlFileError" class="alert alert-danger" role="alert">
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}!</h4>
                                <p class="mb-0">{{__('digiserv.transform_xml_file_error_text') }}</p>{{ $message }}
                            </div>
                            @enderror
                            @error('xmlText')
                            <div id="xmlTExtError" class="alert alert-danger" role="alert">
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}!</h4>
                                <p class="mb-0">{{__('digiserv.transform_xml_text_error_text') }}</p>{{ $message }}
                            </div>
                            @enderror
                            <ul class="nav nav-tabs" id="xmlTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="xml-file-tab" data-toggle="tab" href="#xml-file" role="tab" aria-controls="xml-file" aria-selected="true">
                                        <i class="fas fa-file-code"></i>&nbsp;{{ __('digiserv.transform_file_title') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="xml-text-tab" data-toggle="tab" href="#xml-text" role="tab" aria-controls="xml-text" aria-selected="false">
                                        <i class="fas fa-align-justify"></i>&nbsp;{{ __('digiserv.transform_text_title') }}
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="xmlTabContent">
                                <div class="tab-pane fade show active p-4" id="xml-file" role="tabpanel" aria-labelledby="xml-file-tab">
                                    <label for="xmlFile">{{ __('digiserv.transform_file_title') }}:</label>
                                    <div class="input-group d-flex align-items-stretch">
                                        <input type="file" class="form-control h-auto @error('xmlFile') is-invalid @enderror" id="xmlFile" name="xmlFile" accept="text/xml">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" type="button" id="xmlFileClearButton"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade p-4" id="xml-text" role="tabpanel" aria-labelledby="xml-text-tab">
                                    <label for="xmlText">{{ __('digiserv.transform_text_title') }}:</label>
                                    <textarea class="form-control @error('xmlText') is-invalid @enderror" rows="20" id="xmlText" name="xmlText" placeholder="{{ __('digiserv.transform_text_title') }}"></textarea>
                                </div>
                            </div>
                            <div id="noXmlError" class="alert alert-danger mt-3 alert-dismissible fade show d-none" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}!</h4>
                                <p class="m-0">
                                    {{ __('digiserv.transform_no_xml_error_text') }}
                                </p>
                            </div>
                            <div id="whichXmlError" class="alert alert-danger mt-3 alert-dismissible fade show d-none" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}!</h4>
                                <p class="m-0">
                                    {{ __('digiserv.transform_which_xml_error_text') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="fas fa-chevron-left"></i>&nbsp;{{ __('digiserv.back') }}
                        </a>
                        <button type="submit" id="startTransformSubmitButton" class="btn btn-primary float-right">
                            {{ __('digiserv.next') }}&nbsp;<i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
