<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : null;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Для правообладателей | AnimeFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
<?php
include 'header.php';
?>
<main class="content-container" style="
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background-color: #070720;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 2px solid #ff4444;
">
    <h2 style="color: #ff4444; text-align: center; margin-bottom: 20px; font-size: 28px;">Для правообладателей</h2>
    
    <h5 style="color: #ffffff; font-size: 20px;">Деятельность сайта AnimeFlow осуществляется в соответствии с законодательством Российской Федерации в области защиты информации и авторских прав на контент.</h5>
    
    <p style="color: #ffffff; font-size: 18px;">Все размещенные на ресурсе материалы находятся в свободном доступе и могут быть бесплатно скачаны из интернета. Сбор информации в сети и размещение контента в каталоге производится в автоматическом режиме.</p>
    
    <p style="color: #ffffff; font-size: 18px;">Публикация нелицензионного, похищенного контента и материалов, защищенных авторским правом, не допускается. Администрация размещает только любительские русскоязычные материалы из свободных источников при использовании автоматической системы.</p>
    
    <p style="color: #ffffff; font-size: 18px;">На сайте AnimeFlow публикуются только фрагменты материалов, переведенные на русский язык, а также контент с любительским переводом для ознакомительного просмотра. Размещение оригинальных, непереведенных материалов невозможно.</p>
    
    <p style="color: #ffffff; font-size: 18px;">Администрация ресурса предлагает сотрудничество с правообладателями контента. В случае нарушения прав собственности сайт обязуется убрать неправомерно размещенный материал или предложить выгодные условия сотрудничества правообладателю.</p>
    
    <p style="color: #ffffff; font-size: 18px;">Если вы обнаружили материал, представленный на нашем сайте, который нарушает ваши авторские права, или же дискредитирует вашу компанию, предоставляя неверную или искаженную информацию, пожалуйста, свяжитесь с нами для решения этого вопроса.</p>
    
    <p style="color: #ffffff; font-size: 18px;">Для этого необходимо отправить e-mail с вашего корпоративного почтового ящика, содержащий:</p>
    
    <ul style="color: #ffffff; margin-left: 20px; font-size: 18px;">
        <li>Контактные данные, реквизиты вашей компании;</li>
        <li>Прямую ссылку(ссылки) на материал, который вы считаете спорным;</li>
        <li>Заверенные сканированные копии документов, подтверждающих ваше эксклюзивное право на материал;</li>
        <li>В случае, если вы представляете интересы другой компании – копии документов на посреднические услуги;</li>
    </ul>
    
    <p style="color: #ffffff; font-size: 18px;">На адрес <a href="mailto:animeflow.ru@gmail.com" style="color: #ff4444;">animeflow.ru@gmail.com</a></p>
    
    <p style="color: #ffffff; font-size: 18px;">Вся информация будет проверена, и администрация сайта в кратчайшие сроки свяжется с вами для урегулирования спорного вопроса.</p>
    
    <p style="color: #ffffff; font-size: 18px;">Разрешения для встраивания видео:</p>
    
    <ul style="color: #ffffff; margin-left: 20px; font-size: 18px;">
        <li><a target="_blank" href="https://developers.google.com/youtube/terms/api-services-terms-of-service-ru" style="color: #ff4444;">YouTube.com</a></li>
        <li><a target="_blank" href="https://apiok.ru/ext/video" style="color: #ff4444;">OK.ru</a></li>
        <li><a target="_blank" href="https://rutube.ru/info/agreement/" style="color: #ff4444;">Rutube.ru</a></li>
    </ul>
</main>

<?php
include 'footer.php';
?>

