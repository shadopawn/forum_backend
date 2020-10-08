<?php

class ForumDatabase
{
    private $connection = null;

    function __construct() {
        $servername = "10.0.0.10";
        $username = "root";
        $dbname = "forum";
        try {
            $this->connection = new PDO("mysql:host=$servername;dbname=$dbname", $username);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";
        }catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function __destruct() {
        $this->connection = null;
    }

    public function getTopics(){
        try {
            $sql = "SELECT * FROM topic";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            $dataContainer = array("data" => $results);
            $json = json_encode($dataContainer);
            //echo $json;
            return $json;
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }
}