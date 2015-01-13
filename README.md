Chintz Parser PHP
=================

A PHP parser for [Chintz](https://github.com/pgchamberlin/chintz#what-is-chintz) libraries.

This is a work-in-progress implementation of the Chintz Parser specified at: https://github.com/pgchamberlin/chintz#chintz-parser

## Example usage

Assuming you use Mustache for templates, and default handling for resolved dependencies:

```
<?php

$parser = Chintz_Parser(array(
  'chintz-base-path' => '/absolute/path/to/chintz/library',
  'templater' => new Chintz_Templater_Mustache()
));

$parser->prepare('my-organism');

$data = array(
  'content' => 'This is some data our "my-organism" component knows how to display'
);

echo $parser->render('my-organism', $data);
```

## Is there a demo?

Yes, see: [`chintz-parser-php-demo`](https://github.com/pgchamberlin/chintz-parser-php-demo), which is [hosted also](http://peterchamberlin.com/experiments/chintz-parser-php-demo/index.php).
