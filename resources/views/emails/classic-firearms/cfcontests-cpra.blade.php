<table style="width:100%; margin:10px;" cellpadding="10" cellspacing="10">
    <tr>
        <td>
            New CPRA submission from: {{ $referrer }}
        </td>
    </tr>
</table>

<table style="width:100%; margin:10px; border: 1px solid #CCC;" cellpadding="10" cellspacing="10">
    @foreach ($request->all() as $index => $value )
        <tr>
            <td>{{ $index }}</td>
            <td>{{ $value }}</td>
        </tr>
    @endforeach
</table>
