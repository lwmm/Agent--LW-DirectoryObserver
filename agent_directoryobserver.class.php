<?php

class agent_directoryobserver extends lw_agent
{

    protected $config;
    protected $request;
    protected $controller;


    public function __construct()
    {
        parent::__construct();
        $this->config = $this->conf;
        $this->className = "agent_directoryobserver";
        $this->adminSurfacePath = $this->config['path']['agents'] . "adminSurface/templates/";

        include_once(dirname(__FILE__) . '/Services/Autoloader.php');
        $autoloader = new \AgentDirectoryObserver\Services\Autoloader($this->config);
        $usage = new lw_usage($this->className, "0");
        $this->secondaryUser = $usage->executeUsage();
        
        $response = new \AgentDirectoryObserver\Services\Response();
        $response->setDbObject($this->db);
        $this->controller = new \AgentDirectoryObserver\Controller\DirectoryObserverController($response, $this->config);
    }

    protected function showEdit()
    {
        $dirSizeTable = "";
        $errors = false;
        $selectedDir = false;
        
        if($this->request->getInt("sent") == 1){
            $forminput = $this->request->getRaw("newDir");
            $errors = $this->controller->addObserveDir($forminput["path"], $forminput["label"]);
        }
        
        if($this->request->getRaw("dir")) {
            $selectedDir = $observeDirPath = urldecode($this->request->getRaw("dir"));
            
            $this->controller->setDirectoryObserver($this->config["directoryobserver"]["logtype"],$observeDirPath);

            if($this->request->getInt("scan") == 1) {
                $this->controller->scan();
            }
            
            if($this->request->getInt("delete") == 1 ) {
                $this->controller->deleteDirObserve($observeDirPath);
            }

            $dirSizeTable = $this->controller->getDirSizeTable();
        }
        $view = new \AgentDirectoryObserver\Views\Main();
        return $view->render($dirSizeTable, $forminput, $errors, $selectedDir);
    }

    protected function buildNav()
    {
        $selectedDir = false;
        if($this->request->getRaw("dir")){
            $selectedDir = urldecode($this->request->getRaw("dir"));
        }
        $view = new \AgentDirectoryObserver\Views\Navigation();
        return $view->render($this->controller->getAllDirs(), $selectedDir);
    }

    protected function deleteAllowed()
    {
        return true;
    }
}