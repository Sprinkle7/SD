<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<h1>
    Invoice Status
</h1>

<h2>Billing info</h2>
<p>Order number: {{$user['invoice_number']}}</p>
<p>Full name: {{$user['first_name'] . ' ' . $user['last_name']}}</p>
<p>E-mail: {{$user['email']}}</p>

<p>{{$user['message']}}</p>

</body>
</html>
