<?php

use Symfony\Component\Yaml\Yaml;

class Chintz_Fixture_Parser
{
    private $fixtureSets;
    private $fixtureConfigs;

    public function __construct($params)
    {
        if (!isset($params['chintz-base-path'])) {
            throw new Exception('A Chintz base path is required');
        }
        $this->chintzBasePath = $params['chintz-base-path'];
        if (isset($params['templater'])) {
            $this->templater = $params['templater'];
        }
    }

    public function getData($type, $limit=10)
    {
        $config = $this->getFixtureConfig($type);
        if (isset($config['collection_of'])) {
            return $this->getDataCollection($config['collection_of'], $limit);
        }
        return $this->getFixture($type);
    }

    private function getDataCollection($type, $limit=10)
    {
        $data = array();
        for ($i=0; $i < $limit; $i++) {
            $d = $this->getData($type, 1);
            if (empty($d)) break;
            $data[] = $d;
        }
        return $data;
    }

    private function getFixtureConfig($type)
    {
        if (!isset($this->fixtureConfigs[$type])) {
            $filepath = $this->chintzBasePath . "/fixtures/$type.yaml";
            $data = Yaml::parse($filepath);
            if ($data !== $filepath) {
                $this->fixtureConfigs[$type] = Yaml::parse($this->chintzBasePath . "/fixtures/$type.yaml");
            }
        }
        $this->generateFixtureAttributeMap($type);
        return $this->fixtureConfigs[$type];
    }

    private function generateFixtureAttributeMap($type)
    {
        $map = array();
        $attribs = isset($this->fixtureConfigs[$type]['attributes']) ? $this->fixtureConfigs[$type]['attributes'] : array();
        if (!empty($attribs)) {
            foreach ($attribs as $key => $value) {
                if ($value['type'] == 'collection') {
                    $map[$key] = $value['collection_type'];
                }
            }
        }
        $this->fixtureAttributeMaps[$type] = $map;
    }

    private function getFixture($type)
    {
        if (empty($this->fixtureSets[$type])) {
            $this->fixtureSets[$type] = $this->getFixtureSet($type);
        }

        $currentFixture = current($this->fixtureSets[$type]);
        next($this->fixtureSets[$type]);

        return $currentFixture;
    }

    private function getNamedFixture($type, $name)
    {
        $file = $this->chintzBasePath . "/fixtures/$type/$name.yaml";
        $fixture = Yaml::parse($file);
        $this->generateFixtureAttributeMap($type);
        return $this->resolveCollectionAttributes($type, $fixture);
    }

    private function getFixtureSet($type)
    {
        $fixtureSet = array();
        $fixtureFiles = glob($this->chintzBasePath . "/fixtures/$type/*.yaml");
        foreach ($fixtureFiles as $file) {
            $fixture = Yaml::parse($file);
            $fixture = $this->resolveCollectionAttributes($type, $fixture);
            $fixtureSet[] = $fixture;
        }
        return $fixtureSet;
    }

    private function resolveCollectionAttributes($type, $fixture)
    {
        $map = $this->fixtureAttributeMaps[$type];
        foreach ($map as $attrib => $type) {
            if (isset($fixture[$attrib])) {
                $fixtures = array();
                foreach ($fixture[$attrib] as $name) {
                    $fixtures[] = $this->getNamedFixture($type, $name);
                }
                $fixture[$attrib] = $fixtures;
            }
        }
        return $fixture;
    }
}
