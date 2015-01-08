<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/StaticLibraryCompositor.php';
require_once __DIR__.'/FileSystemAliasLoader.php';

$compositor = new StaticLibraryCompositor();
$compositor->prepare('elementA');

$data = array(
    'title' => 'Chintz Library Rendering Demo',
    'subtitle' => 'Simple example nested items',
    'items' => array(
        array(
            'name' => 'Bobbins',
            'occupation' => 'Philosopher'
        ),
        array(
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
        <?= $compositor->rawCSS() ?>
    </style>
</head>
<body>
    <?= $compositor->render($data) ?>
</body>
</html>
