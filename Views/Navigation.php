<?php

namespace AgentDirectoryObserver\Views;

class Navigation
{
    public function render($dirs, $selectedDir = false)
    {
        $view = new \lw_view(dirname(__FILE__) . '/Templates/Navigation.phtml');
        $view->dirs = $dirs;
        if($selectedDir){
            $view->selectedDir = $selectedDir;
        }
        
        return $view->render();
    }
}