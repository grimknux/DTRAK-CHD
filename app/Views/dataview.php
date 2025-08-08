<html>
    <head>
        <title>User Lists</title>
    </head>
    <body>
        <h1>User Lists</h1>
        <ul>
        <?php foreach($subject as $sub): ?>
            
            <li><?= $sub['subject']; ?>-<?= $sub['abbr']?></li>
            
        <?php endforeach; ?>
        </ul>
    </body>

</html>
