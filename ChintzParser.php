<?php

use Symfony\Component\Yaml\Yaml;

class StaticLibraryCompositor
{
    private $staticLibraryPath = '/vendor/pgchamberlin/static-library-experiment';
    private $elements = array();
    private $js = array();
    private $cssPaths = array();
    private $templatePaths;
    private $baseElement;
    private $baseElementTemplate;

    public function __construct()
    {
        $this->staticLibraryRoot = dirname(__FILE__) . $this->staticLibraryPath;
        $this->loader = new FileSystemAliasLoader($this->staticLibraryRoot);
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

        if (empty($this->baseElement)) {
            // base element is the first we encounter
            $this->setBaseElement($element);
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
        var_dump($this->elements, $this->js, $this->css);

        return $this;
    }

    public function render($data)
    {
        if (empty($this->baseElementTemplate)) {
            // no template to render, so abort!
            return '';
        }
        return $this->mustache->render($this->baseElementTemplate, $data);
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

    private function setBaseElement($element)
    {
        $this->baseElement = $element;
        $this->baseElementTemplate = $this->getElementTemplate($element);
    }

    private function setTemplate($element)
    {
        $this->loader->setTemplate($element, $this->getElementTemplatePath($element));
    }

    private function getElementTemplatePath($element)
    {
        return "/$element/$element.mustache";
    }

    private function getElementTemplate($element)
    {
        if (file_exists($this->staticLibraryRoot . $this->getElementTemplatePath($element))
            && $template = file_get_contents($this->staticLibraryRoot . $this->getElementTemplatePath($element))) {
            return $template;
        }
        return '';
    }

    private function getConfig($name)
    {
        return Yaml::parse($this->staticLibraryRoot . "/$name/$name.yaml");
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
