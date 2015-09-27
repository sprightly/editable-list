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
        'host' => 'mysql:host=127.0.0.1',
        'dbName' => 'sprightly_org_ua',
        'username' => 'sprightly_org_ua',
        'password' => 'cgC6M3yFXusAxWn',
    );

    /**
     * @var \Pdo
     */
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
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : '';
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
                $this->itemModel->remove($id);
                break;
            case 'update':
                $this->itemModel->update(array(
                    'id' => $id,
                    'name' => $name,
                    'count' => $count,
                ));
                break;
            case 'long-polling':
                $result = $this->longPolling();
                break;
        }

        return $result;
    }

    /**
     * Initiate models
     */
    private function initModels()
    {
        $connectionString = $this->dbConfig['host'].';dbname='.$this->dbConfig['dbName'];
        $this->db = new \PDO($connectionString, $this->dbConfig["username"], $this->dbConfig["password"]);
        $this->itemModel = new Item($this->db);
    }

    /**
     * Handle long-polling request, and return fresh table checksum when table will be updated
     * @return array
     * @internal param $name
     * @internal param $count
     */
    private function longPolling()
    {
        set_time_limit(0);

        $receivedTableChecksum = isset($_GET['tableChecksum']) ? (int)$_GET['tableChecksum'] : null;

        while (true) {
            $tableChecksum = $this->getTableChecksum();
            if ($receivedTableChecksum == null || $tableChecksum != $receivedTableChecksum) {
                $result = array(
                    'tableChecksum' => $tableChecksum,
                );

                return $result;
            } else {
                sleep(1);
                continue;
            }
        }

        return false;
    }

    /**
     * Get table checksum
     * @return mixed
     */
    private function getTableChecksum()
    {
        $sql = "CHECKSUM TABLE items";
        $q = $this->db->prepare($sql);
        $q->execute();

        $rows = $q->fetchAll();
        if (is_array($rows) && isset($rows[0]['Checksum'])) {
            $checksum = (int) $rows[0]['Checksum'];
        } else {
            $checksum = null;
        }


        return $checksum;
    }
}