<?php

class Chintz_Handler_ExternalCSS implements Chintz_HandlerInterface
{
    public function format($filePaths, $strategy="links")
    {  
        $strategy = $strategy ?: 'links';
        $strategyMethod = 'get' . strtoupper($strategy);
        if (method_exists($this, $strategyMethod)) {
            return $this->$strategyMethod($filePaths);
        }
        return $filePaths;
    }

    private function getLinks($filePaths)
    {
        $links = '';
        foreach ($filePaths as $path) {
            $links .= "<link rel=\"stylesheet\" href=\"$path\">";
        }
        return $links;
    }
}

