<?php

use Symfony\Component\Yaml\Yaml;

class ChintzParser
{
    // default is the demo library path, for the time being
    private $chintzPath = '/vendor/pgchamberlin/chintz';

    private $elements = array();
    private $js = array();
    private $cssPaths = array();
    private $templatePaths;
    private $baseElement;
    private $baseElementTemplate;

    public function __construct($params=array())
    {
        if (isset($params['chintz-path'])) {
            $this->chintzPath = $params['chintz-path'];
        }
        $this->staticLibraryRoot = dirname(__FILE__) . $this->chintzPath;
        $this->loader = new FileSystemAliasLoader();
        $this->mustache = new Mustache_Engine(
            array(
                'partials_loader' => $this->loader
            )
        );
    }
    
    public function prepare($element)
    {
        if (in_array($element, $this->elements)) {
            // already prepared this one
            return $this;
        }

        $elementConfig = $this->getConfig($element);
        if (!empty($elementConfig['dependencies'])) {
            $this->resolveDependencies($elementConfig['dependencies']);
        }

        $this->setTemplate($element);

        return $this;
    }

    public function dumpState()
    {
        var_dump($this->elements, $this->js, $this->cssPaths);

        return $this;
    }

    public function render($element, $data)
    {
        $template = $this->getElementTemplate($element);
        if (empty($template)) {
            // no template to render, so abort!
            return '';
        }
        return $this->mustache->render($template, $data);
    }

    public function rawCSS()
    {
        $css = '';
        foreach ($this->cssPaths as $cssPath) {
            if (file_exists($this->staticLibraryRoot . '/' . $cssPath)
                && $styles = file_get_contents($this->staticLibraryRoot . '/' . $cssPath)) {
                $css .= $styles;
            }
        }
        return $css;
    }

    private function setTemplate($element)
    {
        $this->loader->setTemplate($element, $this->getElementTemplatePath($element));
    }

    private function getElementTemplatePath($element)
    {
        return $this->getChintzPath($element, "$element.mustache");
    }

    private function getElementTemplate($element)
    {
        if (file_exists($this->getElementTemplatePath($element))
            && $template = file_get_contents($this->getElementTemplatePath($element))) {
            return $template;
        }
        return '';
    }

    private function getChintzPath($element, $filename)
    {
        return current(glob($this->staticLibraryRoot . "/*/$element/$filename"));
    }

    private function getConfig($name)
    {
        return Yaml::parse($this->getChintzPath($name, "$name.yaml"));
    }

    private function resolveDependencies($dependencies)
    {
        if (!empty($dependencies['elements'])) {
            $this->resolveElements($dependencies['elements']);
        }
        if (!empty($dependencies['js'])) {
            $this->resolveJS($dependencies['js']);
        }
        if (!empty($dependencies['css'])) {
            $this->resolveCSS($dependencies['css']);
        }
    }

    private function resolveElements($elements)
    {
        foreach ($elements as $element) {
            $this->prepare($element);
        }
    }

    private function resolveJS($scripts)
    {
        $this->scripts = array_merge($this->scripts, $scripts);
    }

    private function resolveCSS($cssPaths)
    {
        $this->cssPaths = array_merge($this->cssPaths, $cssPaths);
    }
}
