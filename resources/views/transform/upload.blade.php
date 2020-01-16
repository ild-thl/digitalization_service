@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Transformation Step #3</div>
                <div class="card-body">
                    <form method="POST" action="/transform/assignment">

                        @csrf
                        @method('post')

                        <!-- Issuer title -->
                        <label for="xml" class="col-md-4 col-form-label">XML</label>
                        <div class="col-md-4">
                            <textarea cols="50" rows="10" name="xml"></textarea>
                        </div>

                        <div class="row">
                            <div class="col">
                                <input type="button" class="btn btn-danger mt-3" value="Back" onclick="location.href='/transform';">
                            </div>
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
