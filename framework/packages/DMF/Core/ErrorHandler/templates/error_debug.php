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

        h2 {
            width: 100%;
            height: 30px;
            color: #d2c2b5;
            text-align: center;
        }

        h2 a {
            color: #d2c2b5;
        }

        h2 a:hover {
            text-decoration: none;
        }

        .error-message, .error-data, .error-stack-element {
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

        .error-stack-element {
            color: #3a87ad;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }

        .error-stack-element p {
            margin: 0 auto;
            text-align: left;
        }

        .error-stack-element p strong {
            display: inline-block;
            width: 200px;
        }
    </style>
</head>
<body>

<div class="error-message">
    <?php echo $error['message']; ?>
</div>

<div class="error-data">
    <?php echo $error['path'] . ':' . $error['line']; ?>
</div>

<h2>Стек вызовов [<a id="hide-stack" href="#">свернуть</a>]</h2>

<div id="stack">
    <?php if (count($error['stack']) > 0): ?>
    <?php foreach ($error['stack'] as $element): ?>
        <div class="error-stack-element">
            <p><strong>Файл:</strong><?php echo $element['file']; ?></p>

            <p><strong>Строка:</strong><?php echo $element['line'];?></p>

            <p><strong>Класс:</strong><?php echo $element['class']; ?></p>

            <p><strong>Функция/Метод:</strong><?php echo $element['function']; ?></p>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="error-stack-element">
        <p>Стек пуст</p>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript" src="//code.jquery.com/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#hide-stack').on('click', function (event) {
            event.preventDefault();
            var self = $(this);
            var $stack = $('#stack');
            if (self.hasClass('hidden')) {
                self.removeClass('hidden');
                self.text('свернуть');
            }
            else {
                self.addClass('hidden');
                self.text('развернуть');
            }
            $stack.toggle();
        });
    });
</script>
</body>
</html>