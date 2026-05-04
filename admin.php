<?php
session_start();
$password_admin = "Mairie123!";
if (isset($_POST['login_pass']) && $_POST['login_pass'] === $password_admin) { $_SESSION['logged_in'] = true; }
if (!isset($_SESSION['logged_in'])) {
    echo '<form method="POST" style="text-align:center; margin-top:100px; font-family:sans-serif;"><h2>Dashboard Mairie</h2><input type="password" name="login_pass"><button type="submit">Entrer</button></form>';
    exit;
}

$dataFile = 'data.json';
$allPages = json_decode(file_get_contents($dataFile), true);

$currentPage = $_GET['page'] ?? 'accueil';
$content = $allPages[$currentPage];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $allPages[$currentPage] = [
        "titre_menu"     => $_POST['titre_menu'],
        "titre_page"     => $_POST['titre_page'],
        "info_demande"   => $_POST['info_demande'],
        "liste_communes" => $_POST['liste_communes'],
        "alerte_titre"   => $_POST['alerte_titre'],
        "alerte_texte"   => $_POST['alerte_texte']
    ];
    file_put_contents($dataFile, json_encode($allPages, JSON_PRETTY_PRINT));
    
    // --- GÉNÉRATION DU FICHIER HTML ---
    $fileName = ($currentPage == 'accueil') ? 'index.html' : $currentPage . '.html';
    
    $html = "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <title>{$allPages[$currentPage]['titre_page']}</title>
        <style>
            body { font-family: sans-serif; margin: 0; background: #f4f4f4; }
            header { background: #1a4670; color: white; padding: 20px; text-align: center; }
            nav { background: #eee; padding: 10px; text-align: center; }
            nav a { margin: 0 15px; text-decoration: none; color: #1a4670; font-weight: bold; }
            .container { max-width: 900px; margin: 30px auto; background: white; padding: 30px; border-radius: 8px; }
            .grid { display: flex; gap: 20px; }
            .main { flex: 2; }
            .side { flex: 1; background: #ff9999; padding: 20px; border-radius: 8px; color: #900; }
            footer { text-align: center; padding: 20px; color: #777; font-size: 0.8em; }
        </style>
    </head>
    <body>
        <header><h1>Mairie de Mondouzil</h1></header>
        <nav>
            <a href='index.html'>Accueil</a>
            <a href='etat-civil.html'>État Civil</a>
            <a href='urbanisme.html'>Urbanisme</a>
        </nav>
        <div class='container'>
            <h1>{$allPages[$currentPage]['titre_page']}</h1>
            <div class='grid'>
                <div class='main'>
                    <h3>Détails</h3>
                    <p>" . nl2br($allPages[$currentPage]['info_demande']) . "</p>
                    <p><strong>Zones :</strong> {$allPages[$currentPage]['liste_communes']}</p>
                </div>
                <div class='side'>
                    <strong>{$allPages[$currentPage]['alerte_titre']}</strong><br>
                    " . nl2br($allPages[$currentPage]['alerte_texte']) . "
                </div>
            </div>
        </div>
        <footer>© 2026 Mairie de Mondouzil</footer>
    </body>
    </html>";
    
    file_put_contents($fileName, $html);
    header("Location: admin.php?page=$currentPage&success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; height: 100vh; background: #f0f2f5; }
        .sidebar { width: 250px; background: #1a4670; color: white; padding: 20px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 12px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #3498db; color: white; }
        .main-content { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .editor-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 700px; }
        input, textarea { width: 100%; margin: 10px 0 15px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #27ae60; color: white; border: none; padding: 15px; width: 100%; border-radius: 4px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <?php foreach ($allPages as $key => $page): ?>
            <a href="?page=<?php echo $key; ?>" class="<?php echo ($currentPage == $key) ? 'active' : ''; ?>">
                📄 <?php echo $page['titre_menu']; ?>
            </a>
        <?php endforeach; ?>
        <br><br>
        <a href="logout.php" style="color:#ff7675;">🚪 Déconnexion</a>
    </div>

    <div class="main-content">
        <div class="editor-box">
            <h1>Edition : <?php echo $content['titre_menu']; ?></h1>
            <?php if(isset($_GET['success'])) echo "<p style='color:green; font-weight:bold;'>✅ Page HTML générée avec succès !</p>"; ?>
            <form method="POST">
                <input type="hidden" name="titre_menu" value="<?php echo htmlspecialchars($content['titre_menu']); ?>">
                
                <label>Titre de la page</label>
                <input type="text" name="titre_page" value="<?php echo htmlspecialchars($content['titre_page']); ?>">
                
                <label>Contenu principal</label>
                <textarea name="info_demande" rows="5"><?php echo htmlspecialchars($content['info_demande']); ?></textarea>
                
                <label>Liste des secteurs</label>
                <input type="text" name="liste_communes" value="<?php echo htmlspecialchars($content['liste_communes']); ?>">

                <div style="background: #fff5f5; padding: 15px; border: 1px solid #ffcccc;">
                    <label>Titre Alerte</label>
                    <input type="text" name="alerte_titre" value="<?php echo htmlspecialchars($content['alerte_titre']); ?>">
                    <label>Message Alerte</label>
                    <textarea name="alerte_texte" rows="2"><?php echo htmlspecialchars($content['alerte_texte']); ?></textarea>
                </div>
                <br>
                <button type="submit" name="save">METTRE À JOUR CETTE PAGE</button>
            </form>
            <br>
            <a href="<?php echo ($currentPage == 'accueil') ? 'index.html' : $currentPage . '.html'; ?>" target="_blank">➡️ Voir la page générée</a>
        </div>
    </div>
</body>
</html>