<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ошибка</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            font-size: 14px;
        }
        .error-message, .error-data {
            padding: 8px 35px 8px 14px;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            width: 700px;
            margin: 20px auto;
            text-align: center;
        }
        .error-message {
            color: #b94a48;
            background-color: #f2dede;
            border-color: #eed3d7;
        }
        .error-data {
            color: #c09853;
            background-color: #fcf8e3;
            border: 1px solid #fbeed5;
        }
    </style>
</head>
<body>
<div class="error-message">
    <?php echo $message; ?>
</div>
<div class="error-data">
    <?php echo $error_data; ?>
</div>
</body>
</html>