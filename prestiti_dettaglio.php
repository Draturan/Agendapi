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
    $controllo = Prestito::controlloPrestito($prestito);
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
include_once("Rubrica.php");
include_once("models/Libro.php");
$segreteria = new Segreteria();
$libri_disponibili = $segreteria->getLibriDisponibili();
//controllo se siamo in modalità di modifica se il libro è presente nei disponibili altrimenti l'aggiungo
if($mode == "modifica" && isset($prestito_mod)){
    $libro_mod = Libro::getLibroByID($prestito_mod->fk_libro);
    if(!array_key_exists($libro_mod->id,$libri_disponibili)){
        $libri_disponibili[$libro_mod->id] = $libro_mod;
    }
}
// carico la rubrica degli utenti
$rubrica = new Rubrica();
$utenti = $rubrica->getRubrica();

// ordino le liste in ordine alfabetico
function ordinaTitoli($a, $b) {
    return strcmp($a->titolo, $b->titolo);
}
usort($libri_disponibili, "ordinaTitoli");
function ordinaUtenti($a, $b) {
    return strcmp($a->nome, $b->nome);
}
usort($utenti, "ordinaUtenti");
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
                    if(!empty($errori) || !empty($successi)):
                        foreach($avvisi as $tipo=>$avviso):
                            if($avviso != null):
                                foreach($avviso as $num=>$messaggio):
                                    ?>
                                    <p class="<?= $tipo ?>" ><?= array_shift($messaggio) ?></p>
                    <?php endforeach; endif; endforeach; endif; ?>
                </article>
                <article>
                    <form class="form form-prestito" action="prestiti_dettaglio.php<?=
                        ($mode == "modifica" && isset($prestito_mod)) ? "?mode=modifica&id=".$prestito_mod->id : null
                        ?>" name="prestito_post" method="post" onsubmit="return ControllaForm(this)">
                        <div class="form_title">
                            <h3><?= $mode == "modifica" ? "Modifica" : "Inserisci" ?> i dati</h3>
                        </div>
                        <div class="form_input">
                            <div class="form-box-sx">
                                <?= (isset($prestito_mod)) ? '<input style="visibility:hidden;position:absolute" name="id" Value="'.$prestito_mod->id.'"/>' : null?>
                                <label>Libro
                                <select class="input" name="fk_libro" onblur="ControlloImmediato(this);">
                                    <option value="">Seleziona un Libro</option>
                                    <?php foreach($libri_disponibili as $libro):
                                            if(isset($prestito_mod) && $libro->id == $prestito_mod->fk_libro):?>
                                        <option value="<?= $prestito_mod->fk_libro ?>" selected><?= $prestito_mod->nome_libro ?></option>
                                            <?php else: ?>
                                        <option value="<?= $libro->id ?>"><?= $libro->titolo ?></option>
                                    <?php endif; endforeach; ?>
                                </select></label>
                                <label>Utente
                                <select class="input" name="fk_utente" onblur="ControlloImmediato(this);">
                                    <option value="">Seleziona un Utente</option>
                                    <?php foreach($utenti as $utente):
                                            if(isset($prestito_mod) && $utente->id == $prestito_mod->fk_utente):?>
                                        <option value="<?= $prestito_mod->fk_utente ?>" selected><?= $prestito_mod->nome_utente ?></option>
                                        <?php else: ?>
                                        <option value="<?= $utente->id ?>"><?= $utente->nome." ".$utente->cognome ?></option>
                                    <?php endif; endforeach; ?>
                                </select></label>
                                <label>Data di inizio</label>
                                <input type="date" class="input" name="data_inizio" placeholder="Data di inizio" <?=
                                (isset($prestito_mod)) ? 'Value="'.$prestito_mod->data_inizio.'"' : null?> onblur="ControlloImmediato(this);" required />
                                <label>Data di fine</label>
                                <input type="date" class="input" name="data_fine" placeholder="Data di fine" <?= (isset($prestito_mod)) ? 'Value="'.$prestito_mod->data_fine.'"' : null?> onblur="ControlloImmediato(this);" required />
                            </div>
                            <div class="form-box-dx">
                                <label>Data di riconsegna</label>
                                <input type="date" class="input" name="data_riconsegna" placeholder="Data di riconsegna" <?= (isset($prestito_mod)) ? 'Value="'.$prestito_mod->data_riconsegna.'"' : null?> onblur="ControlloImmediato(this);" />
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