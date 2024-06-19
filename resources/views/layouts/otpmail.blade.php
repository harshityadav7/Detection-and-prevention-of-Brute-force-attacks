<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Otp Login</title>
</head>
<body>
    <h1> {{ $mailData['title']}}</h1>
    <h2>{{ $mailData['body']}}</h2>
    <h2> Your Otp for login is {{ $mailData['otp']}}</h2>
    <p> Thank you for Contacting us<p>
</body>
</html>