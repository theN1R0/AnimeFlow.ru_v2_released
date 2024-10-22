<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : null;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Политика конфиденциальности персональных данных | AnimeFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
    <h2 style="color: #ff4444; text-align: center; margin-bottom: 20px; font-size: 28px;">Политика конфиденциальности персональных данных</h2>
    <p style="font-size: 18px; color: #ffffff;">Настоящая Политика конфиденциальности персональных данных (далее – Политика конфиденциальности) действует в отношении всей информации, которую сайт AnimeFlow, расположенный на доменном имени animeflow.ru (а также его субдоменах), может получить о Пользователе во время использования сайта animeflow.ru (а также его субдоменов), его программ и его продуктов.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">1. Определение терминов</h4>
    <p style="font-size: 18px; color: #ffffff;"><b>1.1 В настоящей Политике конфиденциальности используются следующие термины:</b></p>
    <p style="font-size: 18px; color: #ffffff;">1.1.1. <b>«Администрация сайта»</b> (далее – Администрация) – уполномоченные сотрудники на управление сайтом AnimeFlow, которые организуют и (или) осуществляют обработку персональных данных, а также определяют цели обработки персональных данных, состав персональных данных, подлежащих обработке, действия (операции), совершаемые с персональными данными.</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.2. «Персональные данные» - любая информация, относящаяся к прямо или косвенно определенному, или определяемому физическому лицу (субъекту персональных данных).</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.3. «Обработка персональных данных» - любое действие (операция) или совокупность действий (операций), совершаемых с использованием средств автоматизации или без использования таких средств с персональными данными, включая сбор, запись, систематизацию, накопление, хранение, уточнение (обновление, изменение), извлечение, использование, передачу (распространение, предоставление, доступ), обезличивание, блокирование, удаление, уничтожение персональных данных.</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.4. «Конфиденциальность персональных данных» - обязательное для соблюдения Оператором или иным получившим доступ к персональным данным лицом требование не допускать их распространения без согласия субъекта персональных данных или наличия иного законного основания.</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.5. «Сайт <b>AnimeFlow</b>» - это совокупность связанных между собой веб-страниц, размещенных в сети Интернет по уникальному адресу (URL): <b>animeflow.ru</b>, а также его субдоменах.</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.6. «Субдомены» - это страницы или совокупность страниц, расположенные на доменах третьего уровня, принадлежащие сайту AnimeFlow, а также другие временные страницы, внизу которые указана контактная информация Администрации.</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.7. «Пользователь сайта <b>AnimeFlow</b>» (далее Пользователь) – лицо, имеющее доступ к сайту <b>AnimeFlow</b>, посредством сети Интернет и использующее информацию, материалы и продукты сайта <b>AnimeFlow</b>.</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.8. «Cookies» — небольшой фрагмент данных, отправленный веб-сервером и хранимый на компьютере пользователя, который веб-клиент или веб-браузер каждый раз пересылает веб-серверу в HTTP-запросе при попытке открыть страницу соответствующего сайта.</p>
    <p style="font-size: 18px; color: #ffffff;">1.1.9. «IP-адрес» — уникальный сетевой адрес узла в компьютерной сети, через который Пользователь получает доступ на AnimeFlow.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">2. Общие положения</h4>
    <p style="font-size: 18px; color: #ffffff;">2.1. Использование сайта AnimeFlow Пользователем означает согласие с настоящей Политикой конфиденциальности и условиями обработки персональных данных Пользователя.</p>
    <p style="font-size: 18px; color: #ffffff;">2.2. В случае несогласия с условиями Политики конфиденциальности Пользователь должен прекратить использование сайта AnimeFlow.</p>
    <p style="font-size: 18px; color: #ffffff;">2.3. Настоящая Политика конфиденциальности применяется к сайту AnimeFlow. AnimeFlow не контролирует и не несет ответственность за сайты третьих лиц, на которые Пользователь может перейти по ссылкам, доступным на сайте AnimeFlow.</p>
    <p style="font-size: 18px; color: #ffffff;">2.4. Администрация не проверяет достоверность персональных данных, предоставляемых Пользователем.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">3. Предмет политики конфиденциальности</h4>
    <p style="font-size: 18px; color: #ffffff;">3.1. Настоящая Политика конфиденциальности устанавливает обязательства Администрации по неразглашению и обеспечению режима защиты конфиденциальности персональных данных, которые Пользователь предоставляет по запросу Администрации при регистрации на сайте AnimeFlow или при подписке на информационную e-mail рассылку.</p>
    <p style="font-size: 18px; color: #ffffff;">3.2. Персональные данные, разрешённые к обработке в рамках настоящей Политики конфиденциальности, предоставляются Пользователем путём заполнения форм на сайте AnimeFlow и включают в себя следующую информацию:</p>
    <ul style="font-size: 18px; color: #ffffff; margin-left: 20px;">
        <li class="mb-2">3.2.1. фамилию, имя, отчество Пользователя;</li>
        <li class="mb-2">3.2.2. адрес электронной почты (e-mail);</li>
        <li class="mb-2">3.2.3. место жительство Пользователя (при необходимости);</li>
        <li class="mb-2">3.2.4. фотографию (при необходимости);</li>
        <li class="mb-2">3.2.5. Пол (при необходимости).</li>
    </ul>
    <p style="font-size: 18px; color: #ffffff;">3.3. AnimeFlow защищает Данные, которые автоматически передаются при посещении страниц:</p>
    <ul style="font-size: 18px; color: #ffffff; margin-left: 20px;">
        <li class="mb-2">- IP адрес;</li>
        <li class="mb-2">- информация из cookies;</li>
        <li class="mb-2">- информация о браузере;</li>
        <li class="mb-2">- время доступа;</li>
        <li class="mbе 2px;">- реферер (адрес предыдущей страницы).</li>
    </ul>
    <p style="font-size: 18px; color: #ffffff;">3.3.1. Отключение cookies может повлечь невозможность доступа к частям сайта, требующим авторизации.</p>
    <p style="font-size: 18px; color: #ffffff;">3.3.2. AnimeFlow осуществляет сбор статистики об IP-адресах своих посетителей. Данная информация используется с целью предотвращения, выявления и решения технических проблем.</p>
    <p style="font-size: 18px; color: #ffffff;">3.4. Любая иная персональная информация, неоговоренная выше (история посещения, используемые браузеры, операционные системы и т.д.) подлежит надежному хранению и нераспространению, за исключением случаев, предусмотренных в п.п. 5.2. настоящей Политики конфиденциальности.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">4. Цели сбора персональной информации пользователя</h4>
    <p style="font-size: 18px; color: #ffffff;">4.1. Персональные данные Пользователя Администрация может использовать в целях:</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.1. Идентификации Пользователя, зарегистрированного на сайте AnimeFlow для его дальнейшей авторизации.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.2. Предоставления Пользователю доступа к персонализированным данным сайта AnimeFlow.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.3. Установления с Пользователем обратной связи, включая направление уведомлений, запросов, касающихся использования сайта AnimeFlow, обработки запросов и заявок от Пользователя.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.4. Определения места нахождения Пользователя для обеспечения безопасности, предотвращения мошенничества.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.5. Подтверждения достоверности и полноты персональных данных, предоставленных Пользователем.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.6. Создания учетной записи для использования частей сайта AnimeFlow, если Пользователь дал согласие на создание учетной записи.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.7. Уведомления Пользователя по электронной почте.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.8. Предоставления Пользователю эффективной технической поддержки при возникновении проблем, связанных с использованием сайта AnimeFlow.</p>
    <p style="font-size: 18px; color: #ffffff;">4.1.9. Предоставления Пользователю с его согласия специальных предложений, новостной рассылки и иных сведений от имени сайта AnimeFlow.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">5. Способы и сроки обработки персональной информации</h4>
    <p style="font-size: 18px; color: #ffffff;">5.1. Обработка персональных данных Пользователя осуществляется без ограничения срока, любым законным способом, в том числе в информационных системах персональных данных с использованием средств автоматизации или без использования таких средств.</p>
    <p style="font-size: 18px; color: #ffffff;">5.2. Персональные данные Пользователя могут быть переданы уполномоченным органам государственной власти Российской Федерации только по основаниям и в порядке, установленным законодательством Российской Федерации.</p>
    <p style="font-size: 18px; color: #ffffff;">5.3. При утрате или разглашении персональных данных Администрация вправе не информировать Пользователя об утрате или разглашении персональных данных.</p>
    <p style="font-size: 18px; color: #ffffff;">5.4. Администрация принимает необходимые организационные и технические меры для защиты персональной информации Пользователя от неправомерного или случайного доступа, уничтожения, изменения, блокирования, копирования, распространения, а также от иных неправомерных действий третьих лиц.</p>
    <p style="font-size: 18px; color: #ffffff;">5.5. Администрация совместно с Пользователем принимает все необходимые меры по предотвращению убытков или иных отрицательных последствий, вызванных утратой или разглашением персональных данных Пользователя.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">6. Права и обязанности сторон</h4>
    <p style="font-size: 18px; color: #ffffff;"><b>6.1. Пользователь вправе:</b></p>
    <p style="font-size: 18px; color: #ffffff;">6.1.1. Принимать свободное решение о предоставлении своих персональных данных, необходимых для использования сайта AnimeFlow, и давать согласие на их обработку.</p>
    <p style="font-size: 18px; color: #ffffff;">6.1.2. Обновить, дополнить предоставленную информацию о персональных данных в случае изменения данной информации.</p>
    <p style="font-size: 18px; color: #ffffff;">6.1.3. Пользователь имеет право на получение у Администрации информации, касающейся обработки его персональных данных, если такое право не ограничено в соответствии с федеральными законами. Пользователь вправе требовать от Администрации уточнения его персональных данных, их блокирования или уничтожения в случае, если персональные данные являются неполными, устаревшими, неточными, незаконно полученными или не являются необходимыми для заявленной цели обработки, а также принимать предусмотренные законом меры по защите своих прав. Для этого достаточно уведомить Администрацию по указанному E-mail адресу.</p>
    <p style="font-size: 18px; color: #ffffff;"><b>6.2. Администрация обязана:</b></p>
    <p style="font-size: 18px; color: #ffffff;">6.2.1. Использовать полученную информацию исключительно для целей, указанных в п. 4 настоящей Политики конфиденциальности.</p>
    <p style="font-size: 18px; color: #ffffff;">6.2.2. Обеспечить хранение конфиденциальной информации в тайне, не разглашать без предварительного письменного разрешения Пользователя, а также не осуществлять продажу, обмен, опубликование, либо разглашение иными возможными способами переданных персональных данных Пользователя, за исключением п.п. 5.2. настоящей Политики Конфиденциальности.</p>
    <p style="font-size: 18px; color: #ffffff;">6.2.3. Принимать меры предосторожности для защиты конфиденциальности персональных данных Пользователя согласно порядку, обычно используемого для защиты такого рода информации в существующем деловом обороте.</p>
    <p style="font-size: 18px; color: #ffffff;">6.2.4. Осуществить блокирование персональных данных, относящихся к соответствующему Пользователю, с момента обращения или запроса Пользователя, или его законного представителя либо уполномоченного органа по защите прав субъектов персональных данных на период проверки, в случае выявления недостоверных персональных данных или неправомерных действий.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">7. Ответственность сторон</h4>
    <p style="font-size: 18px; color: #ffffff;">7.1. Администрация, не исполнившая свои обязательства, несёт ответственность за убытки, понесённые Пользователем в связи с неправомерным использованием персональных данных, в соответствии с законодательством Российской Федерации, за исключением случаев, предусмотренных п.п. 5.2. и 7.2. настоящей Политики Конфиденциальности.</p>
    <p style="font-size: 18px; color: #ffffff;">7.2. В случае утраты или разглашения Конфиденциальной информации Администрация не несёт ответственность, если данная конфиденциальная информация:</p>
    <p style="font-size: 18px; color: #ffffff;">7.2.1. Стала публичным достоянием до её утраты или разглашения.</p>
    <p style="```html
font-size: 18px; color: #ffffff;">7.2.2. Была получена от третьей стороны до момента её получения Администрацией Ресурса.</p>
    <p style="font-size: 18px; color: #ffffff;">7.2.3. Была разглашена с согласия Пользователя.</p>
    <p style="font-size: 18px; color: #ffffff;">7.3. Пользователь несет полную ответственность за соблюдение требований законодательства РФ, в том числе законов о рекламе, о защите авторских и смежных прав, об охране товарных знаков и знаков обслуживания, но не ограничиваясь перечисленным, включая полную ответственность за содержание и форму материалов.</p>
    <p style="font-size: 18px; color: #ffffff;">7.4. Пользователь признает, что ответственность за любую информацию (в том числе, но не ограничиваясь: файлы с данными, тексты и т. д.), к которой он может иметь доступ как к части сайта AnimeFlow, несет лицо, предоставившее такую информацию.</p>
    <p style="font-size: 18px; color: #ffffff;">7.5. Пользователь соглашается, что информация, предоставленная ему как часть сайта AnimeFlow, может являться объектом интеллектуальной собственности, права на который защищены и принадлежат другим Пользователям, партнерам или рекламодателям, которые размещают такую информацию на сайте AnimeFlow. Пользователь не вправе вносить изменения, передавать в аренду, передавать на условиях займа, продавать, распространять или создавать производные работы на основе такого Содержания (полностью или в части), за исключением случаев, когда такие действия были письменно прямо разрешены собственниками такого Содержания в соответствии с условиями отдельного соглашения.</p>
    <p style="font-size: 18px; color: #ffffff;">7.6. В отношении текстовых материалов (статей, публикаций, находящихся в свободном публичном доступе на сайте AnimeFlow) допускается их распространение при условии, что будет дана ссылка на AnimeFlow.</p>
    <p style="font-size: 18px; color: #ffffff;">7.7. Администрация не несет ответственности перед Пользователем за любой убыток или ущерб, понесенный Пользователем в результате удаления, сбоя или невозможности сохранения какого-либо Содержания и иных коммуникационных данных, содержащихся на сайте AnimeFlow или передаваемых через него.</p>
    <p style="font-size: 18px; color: #ffffff;">7.8. Администрация не несет ответственности за любые прямые или косвенные убытки, произошедшие из-за: использования либо невозможности использования сайта, либо отдельных сервисов; несанкционированного доступа к коммуникациям Пользователя; заявления или поведение любого третьего лица на сайте.</p>
    <p style="font-size: 18px; color: #ffffff;">7.9. Администрация не несет ответственность за какую-либо информацию, размещенную пользователем на сайте AnimeFlow, включая, но не ограничиваясь: информацию, защищенную авторским правом, без прямого согласия владельца авторского права.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">8. Разрешение споров</h4>
    <p style="font-size: 18px; color: #ffffff;">8.1. До обращения в суд с иском по спорам, возникающим из отношений между Пользователем и Администрацией, обязательным является предъявление претензии (письменного предложения или предложения в электронном виде о добровольном урегулировании спора).</p>
    <p style="font-size: 18px; color: #ffffff;">8.2. Получатель претензии в течение 30 календарных дней со дня получения претензии письменно или в электронном виде уведомляет заявителя претензии о результатах рассмотрения претензии.</p>
    <p style="font-size: 18px; color: #ffffff;">8.3. К настоящей Политике конфиденциальности и отношениям между Пользователем и Администрацией применяется действующее законодательство Российской Федерации.</p>
    
    <h4 style="font-size: 22px; color: #ff4444; margin-top: 20px;">9. Дополнительные условия</h4>
    <p style="font-size: 18px; color: #ffffff;">9.1. Администрация вправе вносить изменения в настоящую Политику конфиденциальности без согласия Пользователя.</p>
    <p style="font-size: 18px; color: #ffffff;">9.2. Новая Политика конфиденциальности вступает в силу с момента ее размещения на сайте AnimeFlow, если иное не предусмотрено новой редакцией Политики конфиденциальности.</p>
    <p style="font-size: 18px; color: #ffffff;">9.3. Все предложения или вопросы касательно настоящей Политики конфиденциальности следует сообщать по адресу: animeflow.ru@gmail.com</p>
    <p style="font-size: 18px; color: #ffffff;">9.4. Действующая Политика конфиденциальности размещена на странице по адресу https://animeflow.ru/privacy</p>
    <p style="font-size: 18px; color: #ffffff;">Обновлено: 26 Августа 2024 года</p>
</main>

<?php
include 'footer.php';
?>


