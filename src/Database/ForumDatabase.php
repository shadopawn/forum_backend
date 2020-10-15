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
            $sql = "SELECT id, email, created_time, rank FROM user WHERE id=$userID";

            $users = $this->getAssociativeArrayFromSQL($sql);
            return $users[0];
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    private function getAssociativeArrayFromSQL(string $sql, array $args = []){
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registerUser(string $email, string $password){
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userID = $this->getLastID("user") + 1;

        try {
            $sql = "INSERT INTO user (id, email, password)
                    VALUES (?, ?, ?)";
            $statement = $this->connection->prepare($sql);
            $args = [$userID, $email, $hash];
            $statement->execute($args);
        } catch(PDOException $e) {
            echo $sql . "\n" . $e->getMessage();
        }

        $user = $this->getUser($userID);
        return array("data" => $user);
    }

    private function getLastID(string $tableName, string $id = 'id'){
        try {
            $sql = "SELECT MAX($id) FROM $tableName";
            $maxID = $this->getAssociativeArrayFromSQL($sql);
            return $maxID[0]["MAX($id)"];
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
    }

    public function loginUser(string $email, string $password){
        try {
            $sql = "SELECT id, email, password FROM user WHERE email=?";
            $users = $this->getAssociativeArrayFromSQL($sql, [$email]);
            $user = $users[0];

            if (password_verify($password, $user["password"])){
                return $this->createSession($user["id"]);
            }
        } catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }

    }

    public function createSession($userID){
        $sessionID = $this->getLastID("session", "sessionID") + 1 ?? 1;
        $sessionKey = bin2hex(openssl_random_pseudo_bytes(40));
        try {
            $sql = "INSERT INTO session
                    VALUES (?, ?, ?)";
            $statement = $this->connection->prepare($sql);
            $args = [$sessionID, $sessionKey, $userID];
            $statement->execute($args);
            $sessionInfo = array("sessionID" => $sessionID, "sessionKey" => $sessionKey, "userID" => $userID);
            return array("data" => $sessionInfo);
        } catch(PDOException $error) {
            echo $error->getMessage();
        }
    }

    public function createNewThread(){
        try {
            $sql = "INSERT INTO thread (id, user_id, topic_id, name)
                    VALUES ('2', '1', '1', 'test insert topic')";
            $statement = $this->connection->prepare($sql);
            $statement->execute();
        } catch(PDOException $e) {
            echo $sql . "\n" . $e->getMessage();
        }

    }
}

//$forumDatabase = new ForumDatabase();
//$forumDatabase->loginUser("test@test.com", "test");