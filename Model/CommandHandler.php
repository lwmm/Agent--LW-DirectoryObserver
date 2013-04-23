<?php

namespace AgentDirectoryObserver\Model;

class CommandHandler
{
    protected $db;

    /**
     * @param array $config
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function addObserveDir($dirPath, $dirLabel)
    {        
        $this->db->setStatement("INSERT INTO t:lw_master (lw_object, name, opt1text) VALUES (:lw_object, :name, :opt1text) ");
        $this->db->bindParameter("lw_object", "s", "agent_direcotryobserver");
        $this->db->bindParameter("name", "s", $dirLabel);
        $this->db->bindParameter("opt1text", "s", $dirPath);
        return $this->db->pdbquery();
    }
    
    public function deleteEntryByPath($path)
    {
        $this->db->setStatement("DELETE FROM t:lw_master WHERE lw_object = :lw_object AND opt1text = :opt1text ");
        $this->db->bindParameter("lw_object", "s", "agent_direcotryobserver");
        $this->db->bindParameter("opt1text", "s", $path);
        return $this->db->pdbquery();
    }

}