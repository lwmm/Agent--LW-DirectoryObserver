<?php

namespace AgentDirectoryObserver\Model;

class QueryHandler
{
    protected $db;
    
    /**
     * @param array $config
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function getAllDirs()
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object ORDER by name");
        $this->db->bindParameter("lw_object", "s", "agent_direcotryobserver");
        return $this->db->pselect();
    }
    
    public function checkIfLabelExists($label)
    {
        $this->db->setStatement("SELECt * FROM t:lw_master WHERE lw_object = :lw_object AND name = :name ");
        $this->db->bindParameter("lw_object", "s", "agent_direcotryobserver");
        $this->db->bindParameter("name", "s", $label);
        return $this->db->pselect();
    }
    
    public function checkIfPathExists($path)
    {
        $this->db->setStatement("SELECt * FROM t:lw_master WHERE lw_object = :lw_object AND opt1text = :opt1text ");
        $this->db->bindParameter("lw_object", "s", "agent_direcotryobserver");
        $this->db->bindParameter("opt1text", "s", $path);
        return $this->db->pselect();
    }
}