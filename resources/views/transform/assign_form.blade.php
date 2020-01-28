<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">Tag</th>
        <th scope="col">Value</th>
        <th scope="col">Überführung</th>
      </tr>
    </thead>
    <tbody>
        @foreach( $xmlAsArray as $key => $val)
            <tr>
                <td>{{ $key }}</td>
                <td>{{ $val }}</td>
                <td>Formular....</td>
            </tr>
        @endforeach
    </tbody>
</table>
