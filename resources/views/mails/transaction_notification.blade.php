<!DOCTYPE html>
<html>

<head>
    <title>Transaction Notification</title>
</head>

<body>
    <h1>Transaction Alert</h1>
    <p>Dear {{ $transactionDetails['user_name'] }},</p>
    <p>A {{ $transactionDetails['type'] }} transaction has been carried out on your wallet.</p>
    <ul>
        <li><strong>Amount:</strong> {{ $transactionDetails['amount'] }}</li>
        <li><strong>Type:</strong> {{ ucfirst($transactionDetails['type']) }}</li>
        <li><strong>Category:</strong> {{ $transactionDetails['category'] }}</li>
        <li><strong>Status:</strong> {{ $transactionDetails['status'] }}</li>
        <li><strong>Balance After Transaction:</strong> {{ $transactionDetails['new_balance'] }}</li>
        <li><strong>Reference:</strong> {{ $transactionDetails['reference'] }}</li>
        <li><strong>Date:</strong> {{ $transactionDetails['date'] }}</li>
    </ul>
    <p>If you did not authorize this transaction, please contact support immediately.</p>
</body>

</html>
