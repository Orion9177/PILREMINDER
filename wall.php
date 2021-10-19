<?php
date_default_timezone_set("Europe/Paris");
//require_once("processDisplay.php");
require_once('inc/db_config.php');
?>

<!DOCTYPE html>

<html lang="fr">

    <head>
        <title>PIL'REMINDER</title>
        <meta charset="UTF-8">
        <meta name="author" content="troletba">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Refresh" content="60; url=wall.php">
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/countDown.js"></script>
    </head>

    
    <body>
	
        <header>
            <div class="logo">
                <a href="index.php">
                <img src="https://pilalerte-hyp-bit.dom101.prdres/asset/images/bpce.png">
                </a>
            </div>

            <div class="title">PIL'REMINDER</div>
		</header>
<?php

$req_fetch = "SELECT id, name, action, statut, expiration, TIMEDIFF(expiration, NOW()), TIMEDIFF(NOW(), expiration), mail
FROM $dbtable 
WHERE statut IN ('En cours', 'Expiré')
ORDER BY TIMEDIFF(expiration, NOW()) ASC";

$DB_fetch = $pdo->prepare($req_fetch);
$DB_fetch -> execute();
$data = $DB_fetch->fetchall(PDO::FETCH_ASSOC);

// Pour chaque rappel, calcul du temps écoulé depuis création
foreach ($data as $line){
    $timediff = $line["TIMEDIFF(expiration, NOW())"];
    $name = $line["name"];
    $action = $line["action"];
    $id = $line["id"];
	$mail = $line["mail"];
	$statut = $line["statut"];
	
    
// Si l'expiration est dépassée, la BDD est mise à jour en changeant le statut 
    if($timediff <= '00:00:00'){
        $req_update = "UPDATE $dbtable
        SET statut = 'Expiré'
        WHERE TIMEDIFF (NOW(), expiration)>'00:00' AND statut LIKE 'En cours' AND NOT statut IN ('Traité', 'Annulé')";
        
        $DB_update = $pdo->prepare($req_update);
        $DB_update -> execute();
	}
}



echo '<table>';
    echo '<thead>';
        echo '<tr>';
            echo '<th class="titre">Rappel</th>';
            echo '<th class="titre">Temps restant</th>';
            echo '<th class="titre">Actions</th>';
        echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

$DB_fetch -> execute();
$redirection = 'wall.php';
$i=0;

    while ($row = $DB_fetch -> fetch(PDO::FETCH_ASSOC) ) {
        $secondes = strtotime($row['expiration']) - time();
        if($row['statut'] == 'Expiré'){
            echo '<tr><td class="erreur">'.$row['name'].'</td>';
            echo '<td class="erreur"> <div id="timer'.$i.'"></div></td>';
            echo '<td class="erreur">'.$row['action'].'</td>';
            }

        else{
            echo '<tr><td class="ligne">'.$row['name'].'</td>';
            echo '<td class="ligne"> <div id="timer'.$i.'"></div></td>';
            echo '<td class="ligne"">'.$row['action'].'</td>';
            }  

            echo '<script type="text/javascript">
            createCountDown("timer'.$i.'", "'.$secondes.'")
          </script>';
        $i++;
    }
    echo <<<html
    </tbody>
</table>
html;

$DB_fetch -> closeCursor();
?>

    </body>
</html>