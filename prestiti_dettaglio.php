<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 07/12/2018
 * Time: 15:47
 */

$errori = array();
$successi = array();
$mode = null;

// Controllo presenza di GET
if(isset($_GET['mode'])){
    include_once("models/Prestito.php");
    switch($_GET['mode']){
        case "modifica":
            $mode = "modifica";
            if(isset($_GET['id']) && $_GET['id'] != ""){
                $prestito_by_id = Prestito::getPrestitoByID($_GET['id']);
                if(is_object($prestito_by_id)){
                    $prestito_mod = $prestito_by_id;
                }else{
                    $errori[] = $prestito_by_id[Prestito::ERRORE];
                }
            }
            break;
        default:
            break;
    }
}
// Controllo la presenza di post
if($_POST) {
    include_once("models/Prestito.php");
    $prestito = new Prestito($_POST);
    $risultato = "";
    $controllo = $prestito->controlloPrestito();
    if ($controllo === true) {
        if ($mode === "modifica") {
            $risultato = $prestito->updatePrestito();
            $prestito_mod = $prestito;
        } else {
            $risultato = $prestito->insertPrestito();
        }
    } else {
        $risultato = $controllo;
    }
    switch (key($risultato)) {
        case Prestito::ERRORE:
            $errori[] = $risultato[Prestito::ERRORE];
            break;
        case Prestito::SUCCESSO:
            $successi[] = $risultato[Prestito::SUCCESSO];
            break;
    }
}

// Inglobo negli avvisi gli errori e i successi per mostrarli
$avvisi = array("errori"=>$errori,"successi"=>$successi);

include_once("Segreteria.php");
$segreteria = new Segreteria();
$libri_disp = $segreteria->getLibriDisponibili();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dettaglio Prestito - Lapiary</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!--Javascript-->
    <script type="text/javascript" src="js/controllo_prestito.js"></script>
</head>
<body>
<header class="prestiti-header">
    <div class="strato1">
        <div class="strato2">
            <h1>Lapiary _ </h1>
            <h2><a href="index.php">Homepage</a> > <a href="prestiti.php">Lista prestiti</a> > Dettaglio</h2>
        </div>
    </div>
</header>
<div class="container bcol-prestiti" id="dettaglio">
    <section class="contents">
        <article class="sopra-form">
            <p class="torna_lista"><a href="prestiti.php"> < Torna alla lista prestiti </a></p>
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
            <form class="form form-prestito" action="prestiti_dettaglio.php<?= ($mode == "modifica" && isset($prestito_mod)) ? "?mode=modifica&id=".$prestito_mod->id : null ?>" name="prestito_post" method="post" onsubmit="return ControllaForm(this)">
                <div class="form_title">
                    <h3><?= $mode == "modifica" ? "Modifica" : "Inserisci" ?> i dati</h3>
                </div>
                <div class="form_input">
                    <div class="anagrafiche">
                        <?= (isset($prestito_mod)) ? '<input style="visibility:hidden;position:absolute" name="id" Value="'.$prestito_mod->id.'"/>' : null?>
                        <label>Libro</label>
                        <select class="input" name="libro">
                            <option value="">Seleziona un Libro</option>
                            <?php foreach($libri_disp["libri"] as $key=>$value): ?>
                                <option value="<?= "" ?>"><?= "" ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Utente</label>
                        <select class="input" name="libro">
                            <option value="">Seleziona un utente</option>
                            <?php foreach($libri_disp["libri"] as $key=>$value): ?>

                            <?php endforeach; ?>
                        </select>
                        <label>Data di inizio</label>
                        <input type="date" class="input" name="data_inizio" placeholder="Data di inizio" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->data_di_nascita.'"' : null?> onblur="ControlloImmediato(this);" required />
                        <label>Data di fine</label>
                        <input type="date" class="input" name="data_fine" placeholder="Data di fine" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->data_di_nascita.'"' : null?> onblur="ControlloImmediato(this);" required />
                    </div>
                    <div class="telefono">
                        <label>Data di riconsegna</label>
                        <input type="date" class="input" name="data_riconsegna" placeholder="Data di riconsegna" <?= (isset($utente_mod)) ? 'Value="'.$utente_mod->data_di_nascita.'"' : null?> onblur="ControlloImmediato(this);" required />
                    </div>
                </div>
                <input type="submit" class="submit submit-prestito" name="submit" value="Invia" />
            </form>
        </article>
    </section>
</div>
<footer>
    <p class="piccolino">Lapiary - 2018</p>
</footer>
</body>
</html>