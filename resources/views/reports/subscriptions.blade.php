<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subscriptions Report</title>
</head>
<body>
    <h1>Subscriptions Report</h1>
    <p>Total monthly spend: ${{ number_format($total, 2) }}</p>

    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr>
                <th>Merchant</th>
                <th>Amount</th>
                <th>Next Charge</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->merchant_name }}</td>
                    <td>${{ number_format($subscription->amount, 2) }}</td>
                    <td>{{ $subscription->next_charge_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>