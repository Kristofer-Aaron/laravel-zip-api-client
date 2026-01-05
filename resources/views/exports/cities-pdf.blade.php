<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; font-size: 12px; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>

<h2>Cities Export</h2>

<table>
    <thead>
        <tr>
            <th>ZIP</th>
            <th>City</th>
            <th>County</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cities as $city)
            <tr>
                <td>{{ $city->zip }}</td>
                <td>{{ $city->name }}</td>
                <td>{{ $city->county->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
