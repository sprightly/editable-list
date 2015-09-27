<?php

namespace Table\Models;

/**
 * Class Item
 * Interaction with items on DB level
 * @package Table\Models
 */
class Item
{
    protected $db;

    /**
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param $row
     * @return \StdClass
     */
    private function read($row)
    {
        $result = new \StdClass();
        $result->id = $row["id"];
        $result->name = $row["name"];
        $result->count = $row["count"];

        return $result;
    }

    /**
     * @param $id
     * @return \StdClass
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM items WHERE id = :id";
        $q = $this->db->prepare($sql);
        $q->bindParam(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        $rows = $q->fetchAll();

        return $this->read($rows[0]);
    }

    /**
     * @param $filter
     * @return array
     */
    public function getAll($filter)
    {
        $name = "%".$filter["name"]."%";

        $sql = "SELECT * FROM items WHERE name LIKE :name";
        $q = $this->db->prepare($sql);
        $q->bindParam(":name", $name);
        $q->execute();
        $rows = $q->fetchAll();

        $result = array();
        foreach ($rows as $row) {
            array_push($result, $this->read($row));
        }

        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        $sql = "INSERT INTO items (name, count) VALUES (:name, :count)";
        $q = $this->db->prepare($sql);
        $q->bindParam(":name", $data["name"]);
        $q->bindParam(":count", $data["count"], \PDO::PARAM_INT);
        $q->execute();

        return $this->getById($this->db->lastInsertId());
    }

    /**
     * @param $data
     */
    public function update($data)
    {
        $sql = "UPDATE items SET name = :name, count = :count WHERE id = :id";
        $q = $this->db->prepare($sql);
        $q->bindParam(":name", $data["name"]);
        $q->bindParam(":count", $data["count"], \PDO::PARAM_INT);
        $q->bindParam(":id", $data["id"], \PDO::PARAM_INT);
        $q->execute();
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $sql = "DELETE FROM items WHERE id = :id";
        $q = $this->db->prepare($sql);
        $q->bindParam(":id", $id, \PDO::PARAM_INT);
        $q->execute();
    }
}