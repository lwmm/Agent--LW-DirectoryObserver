<?php

namespace AgentDirectoryObserver\Controller;

class DirectoryObserverController
{

    protected $response;
    protected $config;
    protected $directoryObserver;


    public function __construct($response, $config)
    {
        $this->response = $response;
        $this->config = $config;
    }

    public function setDirectoryObserver($saveType,$observePath)
    {
        try {
            $this->directoryObserver = new \LwDirectoryObserver\Model\DirectoryObserver($this->config, $observePath);
        } catch (\LwDirectoryObserver\Model\ObserveDirectoryNotExistingException $exc) {
            die("Das zu beobachtene Verzeichnis ist nicht vorhanden.");
        } catch (\LwDirectoryObserver\Model\DirectoryObserverLogDirectoryNotExistingException $exc) {
            die("'lw_direcotryobserver' Verzeichnis zum Speichern der Verzeichnisstruktur existiert nicht.");
        } catch (\LwDirectoryObserver\Model\ChangeLogDirectoryNotExistingException $exc) {
            die("Das Change-Log Verzeichnis existiert nicht.");
        } catch (\LwDirectoryObserver\Model\ChangeLogDirectoryNotWritableException $exc) {
            die("Das Change-Log Verzeichnis ist nicht beschreibbar.");
        } catch (\LwDirectoryObserver\Model\JqPlotDirectoryNotWritableException $exc) {
            die("Das Verzeichnis f&uuml;r die JqPlot-Library existiert nicht.");
        }     
        
        if($saveType == "db") {
            $this->directoryObserver->setCommandHandler(new \LwDirectoryObserver\Model\DbCommandHandler($this->response->getDbObject(), $this->config["path"]["resource"] . "lw_logs/lw_directoryobserver/"));
            $this->directoryObserver->setQueryHandler(new \LwDirectoryObserver\Model\DbQueryHandler($this->response->getDbObject()));
        }
        else{
            $this->directoryObserver->setCommandHandler(new \LwDirectoryObserver\Model\TextCommandHandler($this->config["directoryobserver"]["changelog_path"], $this->config["path"]["resource"] . "lw_logs/lw_directoryobserver/"));
            $this->directoryObserver->setQueryHandler(new \LwDirectoryObserver\Model\TextQueryHandler($this->config["directoryobserver"]["changelog_path"]));
        }
    }
    
    public function scan()
    {
        $this->directoryObserver->scan();
    }

    public function getDirSizeTable()
    {
        $endDate = date("Ymd");
        $startDate = date("Ymd", strtotime("-30 days"));
        $view = new \LwDirectoryObserver\Views\DirSizeTable($this->config);
        return $view->render($this->directoryObserver->getCompleteSizeArrayByDateRange($startDate, $endDate), $this->directoryObserver->getObservePath(), $startDate, $endDate);
    }
    
    public function getAllDirs()
    {
        $queryHandler = new \AgentDirectoryObserver\Model\QueryHandler($this->response->getDbObject());
        return $queryHandler->getAllDirs();
    }
    
    public function addObserveDir($path, $label)
    {
        if (substr($path, strlen($path) - 1, strlen($path)) != "/") {
            $path = $path . "/";
        }
        $path = str_replace("//", "/", $path);
        
        $errors = "";
        $queryHandler = new \AgentDirectoryObserver\Model\QueryHandler($this->response->getDbObject());
        $arr = $queryHandler->checkIfLabelExists($label);
        $arr2 = $queryHandler->checkIfPathExists($path);
        if(!empty($arr)){
            $errors = "Label existiert bereits. ";
        }
        if(!empty($arr2)){
            $errors .= "Pfad wird bereits &uuml;berwacht. ";
        }
        if(!is_dir($path)) {
            $errors .= "Pfad existiert nicht. ";
        }
        if(empty($errors) && !empty($path) && !empty($label)){
            $commandHandler = new \AgentDirectoryObserver\Model\CommandHandler($this->response->getDbObject());
            $commandHandler->addObserveDir($path, $label);
        } else{
            return $errors;
        }
    }
    
    public function deleteDirObserve($path)
    {
        $commandHandler = new \AgentDirectoryObserver\Model\CommandHandler($this->response->getDbObject());
        $commandHandler->deleteEntryByPath($path);
        $this->directoryObserver->deleteAllObserveData();
        
        \AgentDirectoryObserver\Services\Page::reload(substr(\AgentDirectoryObserver\Services\Page::getUrl(), 0, strpos(\AgentDirectoryObserver\Services\Page::getUrl(), "index.php"))."admin.php?obj=directoryobserver");
    }
}