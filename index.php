<?php

date_default_timezone_set("Europe/Paris");
require_once("template/header.php");
require_once ("inc/db_config.php");
session_start();
?>

<form method="post" enctype="multipart/form-data" action="view.php">
    <fieldset><legend>Choix du périmètre :</legend>
        <label for="env"> Périmètre :</label>
        <input id="env" list="required-env" name="env" required>
        <datalist id="required-env">
            <option value = "Commun">
            <option value = "Distribué">
            <option value = "Mainframe">
        </datalist>

        <input id="add" name="add" type="submit" value="Ajouter">

    </fieldset>
</form></br>

<?php require_once("template/footer.php");?>