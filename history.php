<?php
require_once('template/header.php');
require_once ("inc/db_config.php");
session_start();

if(!empty($_POST['env'])){
    $_SESSION['env']=$_POST['env'];
    $env = $_SESSION['env'];
}
else{
    $env=$_SESSION['env'];
}
echo '<p><strong> Périmètre : '.$env.'</strong></p><br>';
?>

<!-- Formulaire de recherche de l'historique -->
        <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
                <fieldset> 
                        <legend>Historique</legend>

                        <label for="from">Du :</label>
                        <input id="from" type="date" name="from" required value="">

                        <label for="to">Au :</label>
                        <input id="to" type="date" name="to" required value="">

                        <input id="envoyer" name="envoyer" type="submit" value="Chercher">
                </fieldset>
        </form></br>

        <table>
                <thead>
                        <tr>
                                <th class="titre">Périmètre</th>
								<th class="titre">Date</th>
                                <th class="titre">Rappel</th>
                                <th class="titre">Action</th>
                                <th class="titre">Temps de traitement</th>
                                <th class="titre">Statut</th>
                        </tr>
                </thead>
                <tbody>

<?php

if($search = !isset($_POST['envoyer'])){

		//$req_fetch_history_all="SELECT *, TIMEDIFF(expiration, NOW()), TIMEDIFF(TIMEDIFF(expiration, creation_date), delay)
							//FROM $dbtable
							//ORDER BY creation_date";

        $req_fetch_history_all="SELECT *, TIMEDIFF(expiration, NOW()), TIMEDIFF(TIMEDIFF(expiration, creation_date), delay)
                            FROM $dbtable 
                            WHERE perimetre LIKE '$env' 
                            ORDER BY creation_date";

        $DB_fetch_history_all = $pdo->prepare($req_fetch_history_all);
        $DB_fetch_history_all -> execute();

        $i=0;
       while ($row=$DB_fetch_history_all->fetch(PDO::FETCH_ASSOC)) {
            if($row['statut'] != 'Expiré' && $row['statut'] != 'En cours'){
                $secondes = strtotime($row['expiration']) - time();

                echo'<tr><td class="ligne">'.$row['perimetre'].'</td>';
				echo'<td class="ligne">'.$row['creation_date'].'</td>';
				echo'<td class="ligne">'.$row['name'].'</td>';
                echo'<td class="ligne">'.$row['action'].'</td>';
                echo'<td class="ligne">'.$row['TIMEDIFF(TIMEDIFF(expiration, creation_date), delay)'].'</td>';
                echo'<td class="ligne">'.$row['statut'].'</td></tr>';
            }

            else{
				echo'<tr><td class="ligne">'.$row['perimetre'].'</td>';
				echo'<td class="ligne">'.$row['creation_date'].'</td>';
				echo'<td class="ligne">'.$row['name'].'</td>';
                echo'<td class="ligne">'.$row['action'].'</td>';
				echo'<td class="ligne"><div id="timer'.$i.'"></div></td>';
                echo'<td class="ligne">'.$row['statut'].'</td></tr>';
            }
            echo '<script type="text/javascript">
            createCountDown("timer'.$i.'", "'.$secondes.'")
          </script>';
        $i++;
        }
		$DB_fetch_history_all -> closeCursor();
}
else{
//Requête de recherche dans la table "reminder" de la base "pilreminder"
		$from = isset($_POST['from']) ? $_POST['from'] : NULL;
        $to = isset($_POST['to']) ? $_POST['to'] : NULL;

        $req_fetch_history="SELECT *, TIMEDIFF(expiration, NOW()), TIMEDIFF(TIMEDIFF(expiration, creation_date), delay)
							FROM $dbtable 
							WHERE perimetre LIKE '$env'
                            AND creation_date BETWEEN '$from' AND '$to'
							ORDER BY creation_date";

        $DB_fetch_history = $pdo->prepare($req_fetch_history);
        $DB_fetch_history -> execute();
        $nbr=$DB_fetch_history->rowCount();

        if($nbr == 0){
?>
                <tr>
		        <td colspan=5 class="ligne">Aucun rappel pour la période sélectionnée</td>
                </tr>
<?php

        }
        $j=0;
//Génération du tableau en fonction des paramêtres de recherche avec affichage du temps de traitement ou du temps restant
        while ($row=$DB_fetch_history->fetch(PDO::FETCH_ASSOC)) {
            if($row['statut'] != 'Expiré' && $row['statut'] != 'En cours'){
                $secondes = strtotime($row['expiration']) - time();

                echo'<tr><td class="ligne">'.$row['perimetre'].'</td>';
				echo'<td class="ligne">'.$row['creation_date'].'</td>';
				echo'<td class="ligne">'.$row['name'].'</td>';
                echo'<td class="ligne">'.$row['action'].'</td>';
                echo'<td class="ligne">'.$row['TIMEDIFF(TIMEDIFF(expiration, creation_date), delay)'].'</td>';
                echo'<td class="ligne">'.$row['statut'].'</td></tr>';

            }
            else{

				echo'<tr><td class="ligne">'.$row['perimetre'].'</td>';
				echo'<td class="ligne">'.$row['creation_date'].'</td>';
				echo'<td class="ligne">'.$row['name'].'</td>';
                echo'<td class="ligne">'.$row['action'].'</td>';
				echo'<td class="ligne"><div id="timer'.$j.'"></div></td>';
                echo'<td class="ligne">'.$row['statut'].'</td></tr>';
            }
            echo '<script type="text/javascript">
            createCountDown("timer'.$j.'", "'.$secondes.'")
          </script>';
        $j++;
        }
		$DB_fetch_history -> closeCursor();
 }
?>
                </tbody>
        </table>

<?php
        
require_once('template/footer.php');
?>