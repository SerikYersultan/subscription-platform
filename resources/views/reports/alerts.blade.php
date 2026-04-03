<!DOCTYPE html>
<html>
<head>
    <title>Alerts Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Alerts Report</h1>
    <p>Total alerts: {{ $alerts->count() }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Message</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alerts as $alert)
            <tr>
                <td>{{ $alert->type }}</td>
                <td>{{ $alert->message }}</td>
                <td>{{ $alert->status }}</td>
                <td>{{ $alert->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>