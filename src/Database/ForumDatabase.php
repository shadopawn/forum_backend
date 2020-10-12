<?php

class ForumDatabase
{
    private $connection = null;

    function __construct() {
        $servername = "localhost";
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

    //returns json for all the topics
    public function getTopics(){
        try {
            $sql = "SELECT * FROM topic";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            $dataContainer = array("data" => $results);
            $json = json_encode($dataContainer, JSON_PRETTY_PRINT);
            //echo $json;
            return $json;
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns json for the threads with a specific $topicID
    public function getThreadsByTopic(int $topicID){
        try {
            $sql = "SELECT * FROM thread WHERE topic_id=$topicID";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            $dataContainer = array("data" => $results);
            $json = json_encode($dataContainer, JSON_PRETTY_PRINT);
            //echo $json;
            return $json;
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns json for the threads with associated posts and their users
    public function getThreadWithPostsAndUser(int $threadID){
        try {
            $sql = "SELECT * FROM thread WHERE id=$threadID";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            $thread =  $results[0];
            $thread["posts"] = $this->getPostsWithUser($threadID);
            $threadDataContainer = array("data" => $thread );
            return json_encode($threadDataContainer , JSON_PRETTY_PRINT);
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns associative array of the posts with user who made the post
    public function getPostsWithUser(int $threadID){
        $posts = $this->getPostsByThread($threadID);
        foreach ($posts as &$post){
            $userID = $post['user_id'];
            $user = $this->getUser($userID);
            $post['user'] = $user;
        }
        return $posts;
    }

    //returns associative array of the posts with $threadID
    public function getPostsByThread(int $threadID){
        try {
            $sql = "SELECT * FROM post WHERE thread_id=$threadID";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $posts = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $posts;
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

            $users = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $users[0];
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }
}

//$forumDatabase = new ForumDatabase();
//$forumDatabase->getThreadWithPostsAndUser(1);