<?php

interface Chintz_TemplaterInterface
{
    public function render($name, $data);

    public function setTemplate($name, $path);

    public function getElementFilename($element);
}
