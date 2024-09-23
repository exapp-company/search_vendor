<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            text-align: center;
            margin-bottom: 50px;
            background: #000;
            padding: 30px;
            width: fit-content;
            border-radius: 35px;
            margin: 0 auto;
        }

        .logo img {
            max-width: 150px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            color: #fff;
            background: #1362fe;
            border: 1px solid #1362fe;
            text-decoration: none;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
            outline-color: transparent;
        }

        .button-telegram {
            display: inline-block;
            font-size: 16px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }

        p {
            margin: 10px 0;
            font-size: 16px;
        }

        .message-text {
            box-sizing: border-box;
            font-family: '-apple-system', 'blinkmacsystemfont', 'segoe ui', 'roboto', 'helvetica', 'arial', sans-serif, 'apple color emoji', 'segoe ui emoji', 'segoe ui symbol';
            font-size: 16px;
            line-height: 1.5em;
            margin-top: 35px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
