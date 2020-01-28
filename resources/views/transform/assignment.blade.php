@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Transformation Step #3</div>
                <div class="card-body">
                    {{-- <form method="POST" action="/transform/upload"> --}}
                        {{--
                        @csrf
                        @method('post')
                        --}}
                        @include('transform.assign_form')
                        <div class="row">
                            <div class="col text-right">
                                <input type="submit" class="btn btn-primary mt-3" value="Next">
                            </div>
                        </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
