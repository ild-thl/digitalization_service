@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <form id="transformAssignForm" method="POST" action="{{ route('transform/create') }}">
                    @csrf
                    <input type="hidden" name="xml_uploaded_filename" value="{{ $xml_uploaded_filename }}">
                    <div class="card-header">
                        <h4>{{ __('digiserv.transform') }}</h4>
                        <div class="progress h-auto">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 66.66%; height: 20px;" aria-valuenow="66.6666" aria-valuemin="0" aria-valuemax="100">{{ __('digiserv.transform_progress_step_2') }}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(empty($xml_errors))
                            <div class="alert alert-success mb-0" role="alert">
                                <h4><i class="fas fa-check-circle"></i>&nbsp;{{ __('digiserv.success') }}</h4>
                                <p class="mb-0">{{ __('digiserv.transform_valid_xml') }}</p>
                            </div>
                            
                            @if($elmo_keys->count() == 0)
                                <div class="alert alert-danger mt-4 mb-0" role="alert">
                                    <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                                    <p class="mb-0">
                                        {{ __('digiserv.key_assignments_elmo_empty_text') }}
                                        <a href="{{ route('elmo_keys') }}" class="alert-link" title="{{ __('digiserv.elmo_keys_new_title') }}">{{ __('digiserv.elmo_keys_new_button') }}</a>
                                    </p>
                                </div>
                            @else
                            <div class="alert alert-info mt-3" role="alert">
                                <h4><i class="fas fa-info-circle"></i>&nbsp;{{ __('digiserv.note') }}</h4>
                                <p class="mb-0">{{ __('digiserv.transform_assign_text') }}</p>
                            </div>
                            <div class="form-group my-3 col-md-6 px-0">
                                <label for="selectModuleTag">{{ __('digiserv.transform_select_module_tag_label') }}:</label>
                                <select id="selectModuleTag" name="selectModuleTag" class="form-control" aria-describedby="selectModuleTagHelp" data-required="true">
                                    <option value="-1">---</option>
                                    @foreach($module_tags as $module_tag_id => $module_tag)
                                        <option value="{{ $module_tag->getTag() }}" @if (Cookie::get('selectedModuleTag') == $module_tag->getTag()) selected @endif>
                                            {{ $module_tag->getTag() }} ({{ $module_count[$module_tag_id] }} {{ $module_count[$module_tag_id] == 1 ? __('digiserv.transform_module_sin') : __('digiserv.transform_module_mult') }})
                                        </option>
                                    @endforeach
                                </select>
                                <small id="selectModuleTagHelp" class="form-text text-muted">
                                    {{ __('digiserv.transform_select_module_tag_helptext') }}
                                </small>
                            </div>
                            <div class="form-group my-3 col-md-6 px-0">
                                <label for="selectModulePartTag">{{ __('digiserv.transform_select_modulepart_tag_label') }}:</label>
                                <select id="selectModulePartTag" name="selectModulePartTag" class="form-control" aria-describedby="selectModulePartTagHelp" data-required="true">
                                    <option value="-1">---</option>
                                    @foreach($modulepart_tags as $modulepart_tag)
                                        <option value="{{ $modulepart_tag->getTag() }}" @if (Cookie::get('selectedModulePartTag') == $modulepart_tag->getTag()) selected @endif>
                                            {{ $modulepart_tag->getTag() }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="selectModulePartTagHelp" class="form-text text-muted">
                                    {{ __('digiserv.transform_select_modulepart_tag_helptext') }}
                                </small>
                            </div>
                            <table class="table table-striped table-hover mt-4 mb-0">
                                <thead>
                                    <th scope="col">{{ __('digiserv.transform_table_xml_parent_tag_title') }}</th>
                                    <th scope="col">{{ __('digiserv.transform_table_xml_tag_title') }}</th>
                                    <th scope="col">{{ __('digiserv.transform_table_elmo_title') }}</th>
                                </thead>
                                <tbody>
                                @foreach($xml_tags as $xml_index => $xml_tag)
                                    <tr>
                                        <td class="align-middle">
                                            {{ $xml_tag->getParentTag() }}
                                            <input type="hidden" name="parentTag[{{ $xml_index }}]" value="{{ $xml_tag->getParentTag() }}">
                                        </td>
                                        <td class="align-middle">
                                            {{ $xml_tag->getTag() }}
                                            <input type="hidden" name="tag[{{ $xml_index }}]" value="{{ $xml_tag->getTag() }}">
                                        </td>
                                        <td class="align-middle">
                                            <select size="1" class="form-control" name="assignElmoKey[{{ $xml_index }}]">
                                                <option value="-1">---</option>
                                                @foreach($elmo_keys as $elmo_key)
                                                    <option value="{{ $elmo_key->id }}"
                                                    @php
                                                    $result = App\KeyAssignment::where([['parent', '=', $xml_tag->getParentTag()], ['tag', '=', $xml_tag->getTag()],])->orderBy('updated_at', 'desc')->value('elmo_key_id');
                                                    if($result != null && $result == $elmo_key->id) { echo "selected"; }
                                                    @endphp
                                                    >
                                                        {{ $elmo_key->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </t>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @endif
                        @else
                            <div class="alert alert-danger mb-0" role="alert">
                                <h4><i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.error') }}</h4>
                                <p>{{ __('digiserv.transform_invalid_xml') }}</p>
                                @foreach($xml_errors as $xml_error)
                                    <p>
                                        {!! $xml_error !!}
                                        @if(!$loop->last)
                                            <hr>
                                        @endif
                                    </p>
                                @endforeach
                            </div>
                        @endif
                        
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('transform') }}" class="btn btn-danger">
                            <i class="fas fa-times-circle"></i>&nbsp;{{ __('digiserv.cancel_button_text') }}
                        </a>
                        <button type="reset" id="assignTransformResetButton" class="btn btn-secondary">
                            <i class="fas fa-trash-alt"></i>&nbsp;{{ __('digiserv.reset') }}
                        </button>
                        <button type="submit" id="assignTransformSubmitButton" class="btn btn-primary">
                            {{ __('digiserv.next') }}&nbsp;<i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
