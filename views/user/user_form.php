```html
<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="/assets/ce9417fa/css/bootstrap.css">
    <style>
        body { padding: 20px; max-width: 600px; margin: 0 auto; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Регистрация</h1>
    
    <div class="alert alert-info">
        Для регистрации используйте API запрос:
        <br><strong>POST /register</strong>
    </div>
    
    <h4>Пример через curl:</h4>
    <pre>curl -X POST "https://<?= $_SERVER['HTTP_HOST'] ?>/register" \
  -H "Content-Type: application/json" \
  -d '{"phone": "79123456789", "password": "ваш_пароль"}'</pre>
    
    <h4>Тестовая форма:</h4>
    <form id="registerForm">
        <div class="mb-3">
            <label>Телефон:</label>
            <input type="tel" class="form-control" id="phone" placeholder="79123456789" required>
        </div>
        <div class="mb-3">
            <label>Пароль:</label>
            <input type="password" class="form-control" id="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
    
    <div id="response" class="mt-3"></div>
    
    <script src="/assets/a35572d0/jquery.js"></script>
    <script>
    $('#registerForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/register',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                phone: $('#phone').val(),
                password: $('#password').val()
            }),
            success: function() {
                $('#response').html('<div class="alert alert-success">Успешно!</div>');
            },
            error: function(xhr) {
                var error = xhr.responseJSON?.error || 'Ошибка';
                $('#response').html('<div class="alert alert-danger">' + error + '</div>');
            }
        });
    });
    </script>
</body>
</html>