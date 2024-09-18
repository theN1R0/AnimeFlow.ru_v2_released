<?php
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'; 
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Главная страница -->
    <url>
        <loc>https://animeflow.ru/</loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Страница со списком аниме -->
    <url>
        <loc>https://animeflow.ru/categories</loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Пример страницы случайного аниме -->
    <url>
        <loc>https://animeflow.ru/anime-details?id=<?= htmlspecialchars($random_anime['shikimori_id']) ?></loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    <!-- Контакты -->
    <url>
        <loc>https://animeflow.ru/copyright</loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.5</priority>
    </url>

    <!-- Страницы всех аниме из базы данных -->
    <?php
    include 'db.php';
    $stmt = $conn->query("SELECT shikimori_id, title FROM anime ORDER BY title ASC");
    $animeList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($animeList as $anime): ?>
    <url>
        <loc>https://animeflow.ru/anime-details?id=<?= htmlspecialchars($anime['shikimori_id']) ?></loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
</urlset>
