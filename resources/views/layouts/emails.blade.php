<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f7fa;
                padding: 30px;
            }

            .container {
                background: #ffffff;
                border-radius: 10px;
                padding: 25px;
                border: 1px solid #e1e4e8;
                max-width: 700px;
                margin: auto;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            }

            .logo {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 25px;
            }

            h2 {
                margin-top: 0;
                color: #1b1b18;
            }

            .footer {
                margin-top: 20px;
                font-size: 14px;
                color: #666;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                <img src="{{ $message->embed(public_path('WeSend_logo_text_light_mode.svg')) }}" alt="wesend-logo">
            </div>
            @yield('body')
            <div class="footer">
                <br>
                @yield('footer')
            </div>
        </div>
    </body>
</html>
