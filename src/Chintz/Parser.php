<?php

use Symfony\Component\Yaml\Yaml;

class Chintz_Parser
{
    private $elements = array();
    private $dependencies = array();
    private $dependencyHandlers = array();
    private $templater;

    public function __construct($params=array())
    {
        if (!isset($params['chintz-base-path'])) {
            throw new Exception('A Chintz base path is required');
        }
        $this->chintzBasePath = $params['chintz-base-path'];
        if (isset($params['templater'])) {
            $this->templater = $params['templater'];
        }
        if (is_array($params['handlers'])) {
            $this->dependencyHandlers = $params['handlers'];
        }
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

    public function render($element, $data)
    {
        $template = $this->getElementTemplate($element);
        if (empty($template)) {
            // no template to render, so abort!
            return '';
        }
        return $this->templater->render($template, $data);
    }

    public function getDependencies($name, $strategy=null)
    {
        if (isset($this->dependencyHandlers[$name]) && isset($this->dependencies[$name])) {
            return $this->dependencyHandlers[$name]->format($this->dependencies[$name], $strategy);
        }
        return $this->dependencies[$name];
    }

    private function setTemplate($element)
    {
        $this->templater->setTemplate($element, $this->getElementTemplatePath($element));
    }

    private function getElementTemplatePath($element)
    {
        return $this->getChintzPath($element, $this->templater->getElementFilename($element));
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
        return current(glob($this->chintzBasePath . "/*/$element/$filename"));
    }

    private function getConfig($name)
    {
        return Yaml::parse($this->getChintzPath($name, "$name.yaml"));
    }

    private function resolveDependencies($dependencies)
    {
        foreach ($dependencies as $name => $values) {
            if ($name === 'elements') {
                $this->resolveElementDependencies($values);
            } else {
                $this->resolveStaticDependencies($name, $values);
            }
        }
    }

    private function resolveElementDependencies($elements)
    {
        foreach ($elements as $element) {
            $this->prepare($element);
        }
    }

    private function resolveStaticDependencies($name, $values)
    {
        array_walk(
            $values,
            function(&$value, $key, $base)
            {
                $value = $base . '/' . $value;
            },
            $this->chintzBasePath
        );
        $existingDeps = isset($this->dependencies[$name]) ? $this->dependencies[$name] : array();
        $this->dependencies[$name] = array_merge($existingDeps, $values);
    }
}
