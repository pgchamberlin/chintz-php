<?php

class Chintz_Fixture_Parser
{
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

    public function getData($element)
    {
        // prepare and return data for the element
        return array();
    }
}

