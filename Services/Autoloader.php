<?php

/**
 * This class is an autoloader which load the required file,
 * depending on namespace and classname.
 * The combination of namespace and classname have to be the 
 * same as in your project directory.
 * 
 * @author Andreas Eckhoff <andreas.eckhoff@logic-works.de>
 * @package lw_directoryobserver
 */

namespace AgentDirectoryObserver\Services;

class Autoloader
{

    /**
     * A new autoloader will be registered in the system.
     */
    public function __construct($config)
    {
        $this->config = $config;
        spl_autoload_register(array($this, 'loader'));
    }

    /**
     * Depending on the class name will be the php file loaded.
     * 
     * @param string $className
     */
    private function loader($className)
    {
        if (strstr($className, 'LwDirectoryObserver')) {
            $path = $this->config['path']['package'].'LwDirectoryObserver';
            $filename = str_replace('LwDirectoryObserver', $path, $className);
        }
        else {
            $path = dirname(__FILE__).'/..';
            $filename = str_replace('AgentDirectoryObserver', $path, $className);
        }
        $filename = str_replace('\\', '/', $filename) . '.php';

        if (is_file($filename)) {
            include_once($filename);
        }
    }

}