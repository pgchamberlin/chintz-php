<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/ChintzParser.php';
require_once __DIR__.'/FileSystemAliasLoader.php';

$chintzParser = new ChintzParser();
$chintzParser->prepare('elementA');

$data = array(
    'title' => 'Chintz Library Rendering Demo',
    'items' => array(
        array(
            'subtitle' => 'Simple example nested item',
            'name' => 'Bobbins',
            'occupation' => 'Philosopher'
        ),
        array(
            'subtitle' => 'Another nested item',
            'name' => 'Bobbinella',
            'occupation' => 'Scientist'
        )
    )
);

?>
<!doctype html>
<html>
<head>
    <style type="text/css">
        <?= $chintzParser->rawCSS() ?>
    </style>
</head>
<body>
    <?= $chintzParser->render($data) ?>
</body>
</html>
