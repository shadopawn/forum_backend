<?php
$servername = "localhost";
$username = "root";
$dbname = "forum";

try {
    $connection = new PDO("mysql:host=$servername;dbname=$dbname", $username);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $statement = $connection->prepare("SELECT * FROM topic");
    $statement->execute();

    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $dataContainer = array("data" => $results);
    $json = json_encode($dataContainer);
    echo $json;
} catch(PDOException $error) {
    echo "Error: " . $error->getMessage();
}
$connection = null;