@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Transformation Step #3</div>
                <div class="card-body">

                    {{ $html }}

                    <form method="POST" action="/transform/upload">

                        @csrf
                        @method('post')

                        <div class="row">
{{-- @include('transform.assign_form', ['array' => $xmlAsArray]) --}}
                        </div>

                        <!-- Issuer title -->
                        {{-- <label for="issuer_title" class="col-md-4 col-form-label">Issuer title</label>
                        <input id="issuer_title" type="text" class="form-control @error('issuer_title') is-invalid @enderror" name="issuer_title" placeholder="Issuer title" value="{{ $user->issuer_title }}" required autofocus>
 --}}
                        <div class="row">
                            <div class="col text-right">
                                <input type="submit" class="btn btn-primary mt-3" value="Next">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
