<?php
//header("refresh: 60;");

//---------------------------------------------------------------                                                                     
//
// Auteur          : Trolet Baptiste - troletba - OPEN                                                                                                                                                                       
// Date édition    : 04 août 2021                
//
// Description     : Page d'index avec formulaire d'enreistrement
//                   des rappels ainsi que l'affichage de ceux
//                   "en cours" ou "expirés"                                                                                              
//                                                                                            
// ---------------------------------------------------------------

date_default_timezone_set("Europe/Paris");
require_once("template/header.php");
require_once ("inc/db_config.php");
session_start();
?>

<!-- Formulaire d'ajout en base d'un rappel -->
<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
    <fieldset><legend>Ajout d'un rappel</legend>     

        <label for="reminder">Nom :</label>
        <input id="reminder" type="text" name="reminder" 
        value="<?php if(isset($_SESSION['nom'])) {echo $_SESSION['nom'];}?>" required>

        <label for="time"> Rappel dans (en minutes) :</label>
        <input id="time" type="text" name="time" 
        value="<?php echo '15';?>" required>

        <label for="action"> Actions requises :</label>
        <input id="action" type="text" name="action" 
        value="" required>

        <input id="add" name="add" type="submit" value="Ajouter">

    </fieldset>
</form></br>

<?php

//---------------------------------------------------------------                                                                                   
//
// Description     : Script permettant le traitement et 
//                   l'ajout en BDD du formulaire 
//                   ci-dessus                                                                                              
//                                                                                            
// ---------------------------------------------------------------

$reminder = isset($_POST['reminder']) ? $_POST['reminder'] : NULL;
$time = isset($_POST['time']) ? $_POST['time'] : NULL;
$action = isset($_POST['action']) ? $_POST['action'] : NULL;

//Permet de rajouter X minutes à la date de l'ajout du rappel
$date = date_create(date('Y-m-d H:i:s'));
date_add($date, date_interval_create_from_date_string($time.'minutes'));
$expiration = date_format($date, 'Y-m-d H:i:s');

//Définition du tableau à enregistrer en base suite à la saisie du formulaire
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!empty($reminder && $time)){
        
        $reminder=array
        (
            "name" => $reminder,
            "creation_date" => date('Y-m-d H:i:s'),
            "expiration" => $expiration,
            "action" => $action,
            "delay" => date('H:i:s'),
            "statut" => 0,
        );

//Requête d'insertion en base du formulaire
        $req_add = "INSERT INTO $dbtable (name, creation_date, expiration, action, delay, statut) 
                    VALUE (:name, :creation_date, :expiration, :action, :delay, :statut)";
        $DB_insert = $pdo->prepare($req_add);
        $DB_insert->execute($reminder);
    }
}

//---------------------------------------------------------------                                                                                   
//
// Description     : Script permettant d'afficher les
//                   rappels en cours 
//                   dans le tableau de la page                                                                                              
//                                                                                            
// ---------------------------------------------------------------

// Requête permettant de trouver les rappels en cours ou expirés non traité
// et calcul la différence en la date d'expiration et le temps courant

$req_fetch = "SELECT id, name, action, statut, expiration, TIMEDIFF(expiration, NOW()), TIMEDIFF(NOW(), expiration)
FROM $dbtable 
WHERE statut IN (0, 1)
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
    
// Si l'expiration est dépassée, la BDD est mise à jour en changeant le statut 
    if($timediff = '00:00:00'){
        $req_update = "UPDATE $dbtable
        SET statut = 1
        WHERE TIMEDIFF (NOW(), expiration)>'00:00' AND statut LIKE 0 AND NOT statut IN (2, 3)";
        
        $DB_update = $pdo->prepare($req_update);
        $DB_update -> execute();

//Envoi du mail de rappel au Pilotage BPCE    
        // $destinataire = "baptiste.trolet-ext@natixis.com";
        // //$destinataire = "ld-dsi-inf-pop-pil-spv-backoffice@natixis.com";
        // $headers = "From: dsi-backoffice-supervision@natixis.com\n";
        // $headers .= "Content-Type: text/html; charset=\"utf-8\"";
        
        // $sujet = "Pil'Reminder : ".date("d/m");
        
        // $message = "<p>Bonjour,</p>

        // <p>Le rappel ".$name." a expiré.</p>

        // <p> Merci de réaliser les actions suivantes : ".$action.".</p> 

        // <p> Cordialement,</p> 
        
        // <p><strong style = 'color: #581D74'>Supervision - Back Office</strong></br>
        // <strong style = 'color: #00A193'>BPCE Infogérance & Technologies</br>
        // Tour de Contrôle (TRC) – Supervision Des Services (SDS)</strong></br>
        // 14-18 Avenue du Général de Gaulle - 94220 Charenton-le-Pont</br></p>";
        
        // mail($destinataire,$sujet,$message,$headers);
    }
}

//echo <<<html
echo '<table>';
    echo '<thead>';
        echo '<tr>';
            echo '<th class="titre">Rappel</th>';
            echo '<th class="titre">Temps restant</th>';
            echo '<th class="titre">Actions</th>';
            echo '<th class="titre">Edition</th>';
        echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
//html;

$DB_fetch -> execute();
$i=0;

    while ($row=$DB_fetch->fetch(PDO::FETCH_ASSOC)) {
        echo '<script type="text/javascript">
                    createCountDown("timer'.$i.'", "'.$row['expiration'].'")
              </script>';

        if($row['statut'] != 1){
            echo '<tr><td class="ligne">'.$row['name'].'</td>';
            echo '<td class="ligne"> <div id="timer'.$i.'"></div></td>';
            echo '<td class="ligne">'.$row['action'].'</td>';

            echo '<form method="post" enctype="multipart/form-data" action="'.$_SERVER['SCRIPT_NAME'].'">';
            echo '<td class="ligne"> 
                    <input type="submit" id="treated" name="treated" value="Traité"/>
                    <input type="submit" id="deleted" name="deleted" value="Annulé"/>
                    <input type="hidden" value="'.$row['id'].'" id="id" name="id"/>
                </td>';
            echo '</form>';
        }
        else{
            echo '<tr><td class="erreur">'.$row['name'].'</td>';
            echo '<td class="erreur"> <div id="timer'.$i.'"></div></td>';
            echo '<td class="erreur"">'.$row['action'].'</td>';

            echo '<form method="post" enctype="multipart/form-data" action="'.$_SERVER['SCRIPT_NAME'].'">';
            echo '<td class="erreur"> 
                    <input type="submit" id="treated" name="treated" value="Traité"/>
                    <input type="submit" id="deleted" name="deleted" value="Annulé"/>
                    <input type="hidden" value="'.$row['id'].'" id="id" name="id"/>
                </td>';
            echo '</form>';
        }
        $i++;
    }
    
    echo <<<html
    </tbody>
</table>
html;

$DB_fetch -> closeCursor();

//---------------------------------------------------------------                                                                                   
//
// Description     : Script permettant la MàJ du statut dans
//                   dans le BDD                                                                                              
//                                                                                            
// ---------------------------------------------------------------

$treated = isset($_POST['treated']) ? $_POST['treated'] : NULL;
$deleted = isset($_POST['deleted']) ? $_POST['deleted'] : NULL;
$id = isset($_POST['id']) ? $_POST['id'] : NULL;

        if (!empty($treated)) {
            $req_update_treated = "UPDATE $dbtable SET statut= 2, delay=TIMEDIFF(expiration, NOW()) WHERE id LIKE '$id'";
            $DB_update_treated = $pdo->prepare($req_update_treated);
            $DB_update_treated -> execute();
            header("location: index.php");
        }
        if (!empty($deleted)) {
            $req_update_deleted = "UPDATE $dbtable SET statut = 3, delay=TIMEDIFF(expiration, NOW()) WHERE id LIKE '$id'";
            $DB_update_deleted = $pdo->prepare($req_update_deleted);
            $DB_update_deleted -> execute();
            header("location: index.php");
            }

require_once("template/footer.php");?>