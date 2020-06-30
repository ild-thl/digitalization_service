@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">{{ __('digiserv.elmo_keys_title') }}</h4></div>
                <div class="card-body">
                    @if($keys->count() > 0)
                        <table class="table table-striped table-hover m-0">
                            <thead>
                            <tr>
                                <th scope="col">{{ __('digiserv.elmo_keys_table_id_title') }}</th>
                                <th scope="col">{{ __('digiserv.elmo_keys_table_title_title') }}</th>
                                <th scope="col">{{ __('digiserv.elmo_keys_table_created_title') }}</th>
                                <th scope="col">{{ __('digiserv.elmo_keys_table_updated_title') }}</th>
                                <th scope="col">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($keys as $key)
                                    <tr id="row-elmokey-{{ $key->id }}">
                                        <td class="align-middle">{{ $key->id }}</td>
                                        <td class="align-middle">{{ $key->title }}</td>
                                        <td class="align-middle">
                                            @if($key->created_at == null)
                                                -
                                            @else
                                                {{ \Carbon\Carbon::parse($key->created_at)->format(__('digiserv.format_database_to_date')) }}
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if($key->updated_at == null)
                                                -
                                            @else
                                                {{ \Carbon\Carbon::parse($key->updated_at)->format(__('digiserv.format_database_to_datetime')) }}
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteElmoKeyModal" data-elmokeytitle="{{ $key->title }}" data-elmokeyid="{{ $key->id }}" @if(!$edit) disabled @endif><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info m-0" role="alert">
                            <h4><i class="fas fa-info-circle"></i> {{ __('digiserv.elmo_keys_empty_title') }}</h4>
                            <p class="m-0">
                                {{ __('digiserv.elmo_keys_empty_text') }} 
                            </p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button type="button" class="float-right btn btn-primary" data-toggle="modal" data-target="#newElmoKeyModal" @if(!$edit) disabled @endif>
                        <i class="fas fa-plus"></i> {{ __('digiserv.elmo_keys_new_button')  }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@if($edit)
    <!-- newElmoKeyModal -->
    <div class="modal fade" id="newElmoKeyModal" tabindex="-1" role="dialog" aria-labelledby="newElmoKeyModalTitel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="newElmoKeyForm" action="{{ route('elmo_keys/add') }}">
                <div class="modal-header">
                    <h3 class="modal-title" id="newElmoKeyModalTitle">{{ __('digiserv.elmo_keys_new_title') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newElmoKeyTitle">{{ __('digiserv.elmo_keys') }}:</label>
                        <input type="text" class="form-control" id="newElmoKeyTitle" placeholder="{{ __('digiserv.elmo_keys_new_title') }}">
                    </div>
                    <div class="alert alert-danger my-3 d-none" role="alert" id="newElmoKeyAlertError">
                        <h4><i class="fas fa-times-circle"></i> {{ __('digiserv.error') }}!</h4>
                        <p class="m-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('digiserv.cancel_button_text') }}</button>
                    <button type="submit" class="btn btn-primary" id="newElmoKeySubmitButton">{{ __('digiserv.save_button_text') }}</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <!-- deleteElmoKeyModal -->
    <div class="modal fade" id="deleteElmoKeyModal" tabindex="-1" role="dialog" aria-labelledby="deleteElmoKeyModalTitel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="deleteElmoKeyForm" action="{{ route('elmo_keys/delete') }}">
                <div class="modal-header">
                    <h3 class="modal-title" id="deleteElmoKeyModalTitle">{{ __('digiserv.elmo_keys_delete_title') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning my-3" id="deleteElmoKeyWarning">
                        {!! __('digiserv.elmo_keys_delete_text') !!}
                    </div>
                    <input type="hidden" id="deleteElmoKeyId" value="">
                    <div class="alert alert-danger my-3 d-none" role="alert" id="deleteElmoKeyAlertError">
                        <h4><i class="fas fa-times-circle"></i> {{ __('digiserv.error') }}!</h4>
                        <p class="m-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('digiserv.cancel_button_text') }}</button>
                    <button type="submit" class="btn btn-danger" id="deleteElmoKeySubmitButton">{{ __('digiserv.confirm_button_text') }}</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endif
@endsection
