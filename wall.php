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
        <link rel="stylesheet" href="css/styles_mur.css">
        <script src="js/countDown.js"></script>
    </head>

    
    <body>
<?php

// Requête permettant de trouver les rappels en cours ou expirés non traité 
//et calcul la différence en la date d'expiration et le temps courant

        $req_fetch = "SELECT name, statut, action, expiration, TIMEDIFF(expiration, NOW())
                      FROM $dbtable 
                      WHERE statut IN ('En cours', 'Expiré')
                      ORDER BY TIMEDIFF(expiration, NOW()) ASC";
        
        $DB_fetch = $pdo -> prepare($req_fetch);

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
$i=0;

    while ($row = $DB_fetch -> fetch(PDO::FETCH_ASSOC) ) {
        $secondes = strtotime($row['expiration']) - time();
        if($row['statut'] != 'Expiré'){
            echo '<tr><td class="ligne">'.$row['name'].'</td>';
            echo '<td class="ligne"> <div id="timer'.$i.'"></div></td>';
            echo '<td class="ligne">'.$row['action'].'</td>';
            }

        else{
            echo '<tr><td class="erreur">'.$row['name'].'</td>';
            echo '<td class="erreur"> <div id="timer'.$i.'"></div></td>';
            echo '<td class="erreur"">'.$row['action'].'</td>';
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