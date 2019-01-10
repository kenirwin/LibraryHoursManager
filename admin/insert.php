<?php
include_once ("../config.php");
try { 
    $db = new PDO("mysql:host=$hostname;dbname=$database;charset=$charset", "$username", "$password");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    echo $ex->getMessage();
}

$q = 'insert into presets (name,rank) VALUES (?,?)';
$v = array ('Thanksgiving Break',2);
ExecutePrepared($db,$q,$v);
print($db->lastInsertId());



function ExecutePrepared($db,$query,$values) {
    try {
        print ($query);
        $stmt=$db->prepare($query);
        $stmt->execute($values);
    } catch (PDOException $ex) {
        print $ex->getMessage;
    }
}

?>