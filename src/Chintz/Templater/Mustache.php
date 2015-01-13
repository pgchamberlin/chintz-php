<?php 

class Chintz_Templater_Mustache implements Chintz_TemplaterInterface
{
    private $mustache;
    private $partialLoader;

    public function __construct(Mustache_Engine $mustache, Chintz_Templater_Mustache_FileSystemAliasLoader $partialLoader)
    {
        $this->mustache = $mustache;
        $this->partialLoader = $partialLoader;
    }

    public function render($name, $data)
    {
        return $this->mustache->render($name, $data);
    }

    public function setTemplate($name, $path)
    {
        $this->partialLoader->setTemplate($name, $path);
    }

    public function getElementFilename($element)
    {
        return "$element.mustache";
    }
}
