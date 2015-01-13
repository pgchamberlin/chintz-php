<?php

class Chintz_Handler_CSS implements Chintz_HandlerInterface
{
    public function format($filePaths, $strategy)
    {  
        $strategy = $strategy ?: 'raw';
        $strategyMethod = 'get' . strtoupper($strategy);
        if (method_exists($this, $strategyMethod)) {
            return $this->$strategyMethod($filePaths);
        }
        return $filePaths;
    }

    private function getRaw($filePaths)
    {
        $css = '';
        foreach ($filePaths as $path) {
            if (file_exists($path)
                && $styles = file_get_contents($path)) {
                $css .= $styles;
            }
        }
        return $css;
    }
}
