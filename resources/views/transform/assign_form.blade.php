@foreach($array as $key => $val)
    @if (is_array($val))
        <b>{{ $key }}</b> <br /><br />
        @include('transform.assign_form', ['array' => $val])
    @else
        {{ $key }} - {{ $val }} <br />
    @endif
@endforeach
