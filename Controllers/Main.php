<?php

namespace Table\Controllers;

use Table\Models\Item;

/**
 * Class Main
 * Control main apps interactions
 * @package Table\Controllers
 */
class Main
{
    private $dbConfig = array(
        'db' => "mysql:host=127.0.0.1;dbname=sprightly_org_ua",
        'username' => "sprightly_org_ua",
        'password' => "cgC6M3yFXusAxWn"
    );
    private $db = null;

    /**
     * @var \Table\Models\Item
     */
    private $itemModel = null;

    function __construct()
    {
        $this->initModels();
        $result = $this->generateResult();
        header("Content-Type: application/json");
        echo json_encode($result);
        die();
    }

    /**
     * Generate result for request
     * @return mixed
     */
    private function generateResult()
    {
        $action = $_GET['action'];
        $result = false;

        switch ($action) {
            case 'load':
                $name = isset($_GET['name']) ? $_GET['name'] : '';
                $count = isset($_GET['count']) ? $_GET['count'] : '';
                $result = $this->itemModel->getAll(array(
                    'name' => $name,
                    'count' => $count,
                ));
                break;
        }

        return $result;
    }

    /**
     * Initiate models
     */
    private function initModels()
    {
        $this->db = new \PDO($this->dbConfig["db"], $this->dbConfig["username"], $this->dbConfig["password"]);
        $this->itemModel = new Item($this->db);
    }
}