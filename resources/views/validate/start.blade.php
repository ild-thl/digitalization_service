@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <form id="validateForm" action="{{ route('validate') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header"><h4 class="mb-0">{{ __('digiserv.validate') }}</h4></div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <h4><i class="fas fa-info-circle"></i>&nbsp;{{ __('digiserv.note') }}</h4>
                            <p class="mb-0">
                                {{ __('digiserv.validate_text') }}
                            </p>
                        </div>
                        <div class="form-group">
                            <label for="elmoFile">{{ __('digiserv.validate_file_title') }}:</label>
                            <div class="input-group d-flex align-items-stretch">
                                <input type="file" class="form-control h-auto @error('elmoFile') is-invalid @enderror" id="elmoFile" name="elmoFile" accept="text/xml" required>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" id="elmoFileClearButton"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="fas fa-chevron-left"></i>&nbsp;{{ __('digiserv.back') }}
                        </a>
                        <button type="submit" id="startValidateSubmitButton" class="btn btn-primary float-right">
                            {{ __('digiserv.next') }}&nbsp;<i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
