<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $error['code'] . ' &bull; ' . substr($error['message'], 0, 32) . '...'; ?></title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            font-size: 14px;
        }

        .error-message {
            padding: 8px 35px 8px 14px;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            width: 700px;
            margin: 20px auto;
            text-align: center;
            color: #b94a48;
            background-color: #f2dede;
            border-color: #eed3d7;
        }

    </style>
</head>
<body>

<div class="error-message">
    <?php echo $error['message']; ?>
</div>

</body>
</html>