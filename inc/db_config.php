<?php

//---------------------------------------------------------------                                                                     
//
// Auteur          : Trolet Baptiste - troletba - OPEN                                                                                                                                                                       
// Date édition    : 04 août 2021                
//
// Description     : Paramêtres de bases de données                                                                                               
//                                                                                            
// ---------------------------------------------------------------

// Variable

$hostname='localhost';
$username='root';
$password='root';
$dbname='pilreminder';
$dbtable= "reminder";

// Connexion

try{
    $pdo = new PDO ("mysql:host=$hostname; dbname=$dbname; charset=utf8", $username, $password);
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
