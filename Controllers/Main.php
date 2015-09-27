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
        'password' => "cgC6M3yFXusAxWn",
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
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
        $count = isset($_REQUEST['count']) ? $_REQUEST['count'] : '';
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : '';
        $result = '';

        switch ($action) {
            case 'load':
                $result = $this->itemModel->getAll(array(
                    'name' => $name,
                    'count' => $count,
                ));
                break;
            case 'insert':
                $result = $this->itemModel->insert(array(
                    'name' => $name,
                    'count' => $count,
                ));
                break;
            case 'delete':
                $this->itemModel->remove( $id );
                break;
            case 'update':
                $this->itemModel->update(array(
                    'id' => $id,
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