<?php
$servername = "localhost";
$username = "root";
$dbname = "forum";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM topic");
    $stmt->execute();

    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
        echo $k . " : " . $v ."\n";
    }
} catch(PDOException $error) {
    echo "Error: " . $error->getMessage();
}
$conn = null;