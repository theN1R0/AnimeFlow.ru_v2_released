<?php
session_start();
include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$username = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $username = $user['username'];
    }
}
$error_message = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хеширование пароля
    $role = 'user';

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);

    if ($stmt->rowCount() > 0) {
        $error_message = "Пользователь с таким логином или email уже существует!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role])) {
            echo "Регистрация успешна!";
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header("Location: /");
        } else {
            echo "Ошибка SQL: " . $stmt->errorInfo()[2];
        }
    }
}

if (isset($_POST['login-btn'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: /"); // Перенаправление на главную страницу
    } else {
        $error_message = "Неверный логин/email или пароль!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Авторизация | AnimeFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" type="text/css">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="img/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="img/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
    <div id="preloder">
        <div class="loader"></div>
    </div>
 <?php include 'header.php'; ?>
    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="nb__text">
                        <h2>Авторизация</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
<section class="login spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div id="auth-form">
                    <div class="login__form" id="login-form">
                        <h3>Вход</h3>
                        <?php if ($error_message): ?>
                            <div style="color: red;"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form action="login" method="POST">
                            <div class="input__item">
                                <input type="text" name="login" placeholder="Логин или Email" required>
                                <span class="icon_mail"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Пароль" required>
                                <span class="icon_lock"></span>
                            </div>
                            <button type="submit" name="login-btn" class="site-btn">Войти</button>
                        </form>
                    </div>
                    <div class="login__form" id="register-form" style="display: none;">
                        <h3>Регистрация</h3>
                        <?php if ($error_message): ?>
                            <div style="color: red;"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form action="login" method="POST">
                            <div class="input__item">
                                <input type="text" name="username" placeholder="Имя пользователя" required>
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="email" name="email" placeholder="Email" required>
                                <span class="icon_mail"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Пароль" required>
                                <span class="icon_lock"></span>
                            </div>
                            <button type="submit" name="register" class="site-btn">Зарегестрироваться</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login__register">
                    <h3 id="switch-text">Нет аккаунта?</h3>
                    <a href="#" id="switch-button" class="primary-btn">Зарегестрироваться</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/player.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.getElementById('switch-button').addEventListener('click', function (e) {
            e.preventDefault();
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const switchText = document.getElementById('switch-text');
            const switchButton = document.getElementById('switch-button');

            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                switchText.innerText = "Ещё нет аккаунта?";
                switchButton.innerText = 'Зарегестрироваться';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                switchText.innerText = 'Уже есть аккаунт?';
                switchButton.innerText = 'Войти';
            }
        });
    </script>
</body>
</html>
