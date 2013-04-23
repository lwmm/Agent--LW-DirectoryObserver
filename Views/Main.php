<?php

namespace AgentDirectoryObserver\Views;

class Main
{
    public function render($dirSizeTable, $forminput = false, $errors = false, $selectedDir = false)
    {
        $view = new \lw_view(dirname(__FILE__) . '/Templates/DirectoryInfo.phtml');
        $view->url = substr(\AgentDirectoryObserver\Services\Page::getUrl(), 0, strpos(\AgentDirectoryObserver\Services\Page::getUrl(), "index.php"))."admin.php?obj=directoryobserver";
        $view->dirSizeTable = $dirSizeTable;
        if($errors) {
            $view->errors = $errors;
        }
        
        if($forminput){
            $view->forminput = $forminput;
        }
        
        if($selectedDir) {
            $view->selectedDir = "&dir=".urlencode($selectedDir);
        }
        else {
            $view->selectedDir = "";
        }
        return $view->render();
    }
}