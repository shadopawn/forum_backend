<?php

class ForumDatabase
{
    private $connection = null;

    function __construct() {
        $databaseConfig = require(__DIR__ .'/../../config/config.php');
        $servername = $databaseConfig["severname"];
        $username = $databaseConfig["username"];
        $dbname = $databaseConfig["dbname"];
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

    //returns associative array for all the topics
    public function getTopics(){
        try {
            $sql = "SELECT * FROM topic";

            $results = $this->getAssociativeArrayFromSQL($sql);
            return array("data" => $results);
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns associative array for the threads with a specific $topicID
    public function getThreadsByTopic(int $topicID){
        try {
            $sql = "SELECT * FROM thread WHERE topic_id=$topicID";

            $results = $this->getAssociativeArrayFromSQL($sql);
            return array("data" => $results);
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns associative array for the threads with associated posts and their users
    public function getThreadWithPostsAndUser(int $threadID){
        try {
            $sql = "SELECT * FROM thread WHERE id=$threadID";

            $results = $this->getAssociativeArrayFromSQL($sql);
            $thread =  $results[0];
            $thread["posts"] = $this->getPostsWithUser($threadID);
            return array("data" => $thread );
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns associative array of the posts with user who made the post
    private function getPostsWithUser(int $threadID){
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

            $posts = $this->getAssociativeArrayFromSQL($sql);
            return $posts;
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    //returns associative array of the user with $userID
    private function getUser(int $userID){
        try {
            $sql = "SELECT * FROM user WHERE id=$userID";

            $users = $this->getAssociativeArrayFromSQL($sql);
            return $users[0];
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    private function getAssociativeArrayFromSQL($sql){
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}

//$forumDatabase = new ForumDatabase();
//$forumDatabase->getThreadWithPostsAndUser(1);