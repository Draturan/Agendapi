<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 21:16
 */
$errori = array();
$successi = array();
$mode = null;

// Controllo presenza di GET
if(isset($_GET['mode'])){
    include_once("models/Utente.php");
    switch($_GET['mode']){
        case "modifica":
            $mode = "modifica";
            if(isset($_GET['id']) && $_GET['id'] != ""){
                $utente_by_id = Utente::getUtenteByID($_GET['id']);
                if(is_object($utente_by_id)){
                    $utente_mod = $utente_by_id;
                }else{
                    $errori[] = $utente_by_id[Utente::ERRORE];
                }
            }
            break;
        default:
            break;
    }
}
// Controllo la presenza di post
if($_POST) {
    include_once("models/Utente.php");
    $utente = new Utente($_POST);
    $risultato = "";
    $controllo = $utente->controlloUtente();
    if ($controllo === true) {
        if ($mode === "modifica") {
            $risultato = $utente->updateUtente();
            $utente_mod = $utente;
        } else {
            $risultato = $utente->insertUtente();
        }
    } else {
        $risultato = $controllo;
    }
    switch (key($risultato)) {
        case Utente::ERRORE:
            $errori[] = $risultato[Utente::ERRORE];
            break;
        case Utente::SUCCESSO:
            $successi[] = $risultato[Utente::SUCCESSO];
            break;
    }
}

// Inglobo negli avvisi gli errori e i successi per mostrarli
$avvisi = array("errori"=>$errori,"successi"=>$successi);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dettaglio Utente - Lapiary</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!--Javascript-->
    <script type="text/javascript" src="js/controllo_utente.js"></script>
</head>
<body>
    <header class="utenti-header">
        <div class="strato1">
            <div class="strato2">
                <h1>Lapiary _ </h1>
                <h2><a href="index.php">Homepage</a> > <a href="utenti.php">Lista utenti</a> > Dettaglio</h2>
            </div>
        </div>
    </header>
    <div class="container" id="dettaglio">
        <section class="contents">
            <article class="sopra-form">
                <p class="torna_lista"><a href="utenti.php"> < Torna alla lista contatti </a></p>
                <?php
                /* Mostro gli errori */
                if(empty($errori) || empty($successi)):
                    foreach($avvisi as $tipo=>$avviso):
                        if($avviso != null):
                            foreach($avviso as $num=>$messaggio):
                                ?>
                                <p class="<?= $tipo ?>" ><?= array_shift($messaggio) ?></p>
                <?php endforeach; endif; endforeach; endif; ?>
            </article>
            <article>
                <form class="form form-utente" action="utenti_dettaglio.php<?= ($mode == "modifica" && isset($utente_mod)) ? "?mode=modifica&id=".$utente_mod->id : null ?>" name="utente_post" method="post" onsubmit="return ControllaForm(this)">
                    <div class="form_title">
                        <h3><?= $mode == "modifica" ? "Modifica" : "Inserisci" ?> i dati</h3>
                    </div>
                    <div class="form_input">
                        <div class="anagrafiche">
                            <?= (isset($utente_mod)) ? '<input style="visibility:hidden;position:absolute" name="id" Value="'.$utente_mod->id.'"/>' : null?>
                            <label>Nome</label>
                            <input type="text" class="input" name="nome" placeholder="es. Mario" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->nome.'"' : null?> onblur="ControlloImmediato(this);" required />
                            <label>Cognome</label>
                            <input type="text" class="input" name="cognome" placeholder="es. Rossi" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->cognome.'"' : null?> onblur="ControlloImmediato(this);" required />
                            <label>Data di nascita</label>
                            <input type="date" class="input" name="datanascita" placeholder="Data di nascita" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->data_di_nascita.'"' : null?> onblur="ControlloImmediato(this);" required />
                            <label>CAP</label>
                            <input type="text" class="input" name="cap" placeholder="es. 00100" maxlength="5" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->cap.'"' : null?> onblur="ControlloImmediato(this);" required />
                            <label>E-mail</label>
                            <input type="email" class="input" name="email" placeholder="es. mario.rossi@gmail.com" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->email.'"' : null?> onblur="ControlloImmediato(this);" required />
                        </div>
                        <div class="telefono">
                            <label>Recapito #1</label>
                            <div class="input_inline">
                                <input type="text" class="input" name="tipo1" placeholder="Tipo" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->num_telefono[0]['tipo'].'"' : null?> onblur="ControlloImmediato(this)" />
                                <input type="tel" class="input" name="telefono1" placeholder="es. 0212312345" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->num_telefono[0]['telefono'].'"' : null?> onblur="ControlloImmediato(this)" />
                                <?= (isset($utente_mod)) ? '<input style="visibility:hidden;position:absolute" name="num_id1" Value="'.$utente_mod->num_telefono[0]['num_id'].'"/>' : null?>
                            </div>
                            <label>Recapito #2</label>
                            <div class="input_inline">
                                <input type="text" class="input" name="tipo2" placeholder="Tipo" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->num_telefono[1]['tipo'].'"' : null?> onblur="ControlloImmediato(this)" />
                                <input type="tel" class="input" name="telefono2" placeholder="es. 0212312345" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->num_telefono[1]['telefono'].'"' : null?> onblur="ControlloImmediato(this)" />
                                <?= (isset($utente_mod)) ? '<input style="visibility:hidden;position:absolute" name="num_id2" Value="'.$utente_mod->num_telefono[1]['num_id'].'"/>' : null?>
                            </div>
                            <label>Recapito #3</label>
                            <div class="input_inline">
                                <input type="text" class="input" name="tipo3" placeholder="Tipo" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->num_telefono[2]['tipo'].'"' : null?> onblur="ControlloImmediato(this)" />
                                <input type="tel" class="input" name="telefono3" placeholder="es. 0212312345" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->num_telefono[2]['telefono'].'"' : null?> onblur="ControlloImmediato(this)" />
                                <?= (isset($utente_mod)) ? '<input style="visibility:hidden;position:absolute" name="num_id3" Value="'.$utente_mod->num_telefono[2]['num_id'].'"/>' : null?>
                            </div>
                        </div>
                    </div>
                    <input type="submit" class="submit submit-utente" name="submit" value="Invia" />
                </form>
            </article>
        </section>
    </div>
    <footer>
        <p class="piccolino">Lapiary - 2018</p>
    </footer>
</body>
</html>
