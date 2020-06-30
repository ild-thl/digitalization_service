@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">{{ __('digiserv.key_assignments_title') }}</h4></div>
                <div class="card-body">
                    <div id="accordion">
                        @forelse($keys as $key)
                        <div class="card">
                            <div class="card-header" id="heading-{{ $key->id }}">
                                <h5 class="mb-0">
                                    <button class="btn btn-link @if(!$loop->first) collapsed @endif p-0" data-toggle="collapse" data-target="#collapse-{{ $key->id }}" aria-expanded="true" aria-controls="collapse-{{ $key->id }}">
                                        <i class="fas fa-chevron-right mr-2" aria-hidden="true"></i>{{ $key->title }}
                                    </button>
                                </h5>
                            </div> 
                            <div id="collapse-{{ $key->id }}" class="collapse @if($loop->first) show @endif" aria-labelledby="heading-{{ $key->id }}" data-parent="#accordion">
                                <div class="card-body">
                                    @if(($key->keyAssignment->count() > 0))
                                        <table class="table table-striped table-sm table-hover m-0">
                                            <thead>
                                                <th scope="col">{{ __('digiserv.key_assignments_table_id_title') }}</th>
                                                <th scope="col">{{ __('digiserv.key_assignments_table_parent_title') }}</th>
                                                <th scope="col">{{ __('digiserv.key_assignments_table_tag_title') }}</th>
                                                <th scope="col">{{ __('digiserv.key_assignments_table_created_title') }}</th>
                                                <th scope="col">{{ __('digiserv.key_assignments_table_updated_title') }}</th>
                                                <th scope="col">&nbsp;</th>
                                            </thead>
                                            <tbody>
                                            @foreach($key->keyAssignment as $key_assignment)
                                                <tr id="row-keyassignment-{{ $key_assignment->id }}">
                                                    <td class="align-middle">{{ $key_assignment->id }}</td>
                                                    <td class="align-middle">
                                                        @if($key_assignment->parent == null)
                                                            -
                                                        @else
                                                            {{ $key_assignment->parent }}
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">{{ $key_assignment->tag }}</td>
                                                    <td class="align-middle">
                                                        @if($key_assignment->created_at == null)
                                                            -
                                                        @else
                                                            {{ \Carbon\Carbon::parse($key_assignment->created_at)->format(__('digiserv.format_database_to_date')) }}
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($key_assignment->updated_at == null)
                                                            -
                                                        @else
                                                            {{ \Carbon\Carbon::parse($key_assignment->updated_at)->format(__('digiserv.format_database_to_datetime')) }}
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteKeyAssignmentModal" data-keyassignmentid="{{ $key_assignment-> id }}" data-keyassignmenttag="{{ $key_assignment->tag }}" data-keyassignmentelmokey="{{ $key->title }}" @if(!$edit) disabled @endif><i class="fas fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info m-0" role="alert">
                                            <h4><i class="fas fa-info-circle"></i> {{ __('digiserv.key_assignments_empty_title') }}</h4>
                                            <p class="m-0">
                                                {{ __('digiserv.key_assignments_empty_text') }}
                                                <a href="{{ route('transform') }}" class="alert-link" title="{{ __('digiserv.start_transform') }}">{{ __('digiserv.start_transform') }}</a>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info m-0" role="alert">
                            <h4><i class="fas fa-info-circle"></i> {{ __('digiserv.key_assignments_elmo_empty_title') }}</h4>
                            <p class="m-0">
                                {{ __('digiserv.key_assignments_elmo_empty_text') }} 
                                <a href="{{ route('elmo_keys') }}" class="alert-link" title="{{ __('digiserv.elmo_keys_new_title') }}">{{ __('digiserv.elmo_keys_new_button') }}</a>
                            </p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($edit)
    <!-- deleteKeyAssignmentModal -->
    <div class="modal fade" id="deleteKeyAssignmentModal" tabindex="-1" role="dialog" aria-labelledby="deleteKeyAssignmentModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="deleteKeyAssignmentForm" action="{{ route('key_assignments/delete') }}">
                <div class="modal-header">
                    <h3 class="modal-title" id="deleteKeyAssignmentModalTitle">{{ __('digiserv.key_assignments_delete_title') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning my-3" id="deleteKeyAssignmentWarning">
                        {!! __('digiserv.key_assignments_delete_text') !!}
                    </div>
                    <input type="hidden" id="deleteKeyAssignmentId" value="">
                    <div class="alert alert-danger my-3 d-none" role="alert" id="deleteKeyAssignmentAlertError">
                        <h4><i class="fas fa-times-circle"></i> {{ __('digiserv.error') }}!</h4>
                        <p class="m-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('digiserv.cancel_button_text') }}</button>
                    <button type="submit" class="btn btn-danger" id="deleteKeyAssignmentSubmitButton">{{ __('digiserv.confirm_button_text') }}</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endif
@endsection
