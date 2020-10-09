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

    public function getThreadsByTopic(int $topicID){
        try {
            $sql = "SELECT * FROM thread WHERE topic_id=$topicID";
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

    public function getThreadWithPostsAndUser(int $threadID){

    }

    public function appendUserToPosts(int $threadID){
        $posts = $this->getPostsByThread($threadID);
        foreach ($posts as &$post){
            $userID = $post['user_id'];
            $user = $this->getUser($userID);
            $post['user'] = $user;
        }
        echo json_encode($posts, JSON_PRETTY_PRINT);
    }

    public function getPostsByThread(int $threadID){
        try {
            $sql = "SELECT * FROM post WHERE thread_id=$threadID";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns associative array of the user with $userID
    public function getUser(int $userID){
        try {
            $sql = "SELECT * FROM user WHERE id=$userID";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $results[0];
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }
}

$forumDatabase = new ForumDatabase();
$forumDatabase->appendUserToPosts(1);