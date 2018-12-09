<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 23:21
 */

$errori = array();
$successi = array();
$mode = null;

// Controllo presenza di GET
if(isset($_GET['mode'])){
    include_once("models/Libro.php");
    switch($_GET['mode']){
        case "modifica":
            $mode = "modifica";
            if(isset($_GET['id']) && $_GET['id'] != ""){
                $libro_by_id = Libro::getLibroByID($_GET['id']);
                if(is_object($libro_by_id)){
                    $libro_mod = $libro_by_id;
                }else{
                    $errori[] = $libro_by_id[Libro::ERRORE];
                }
            }
            break;
        default:
            break;
    }
}
// Controllo la presenza di post
if($_POST) {
    include_once("models/Libro.php");
    $libro = new Libro($_POST);
    $risultato = "";
    $controllo = $libro->controlloLibro();
    if ($controllo === true) {
        if ($mode === "modifica") {
            $risultato = $libro->updateLibro();
            $libro_mod = $libro;
        } else {
            $risultato = $libro->insertLibro();
        }
    } else {
        $risultato = $controllo;
    }
    switch (key($risultato)) {
        case Libro::ERRORE:
            $errori[] = $risultato[Libro::ERRORE];
            break;
        case Libro::SUCCESSO:
            $successi[] = $risultato[Libro::SUCCESSO];
            break;
    }
}

// Inglobo negli avvisi gli errori e i successi per mostrarli
$avvisi = array("errori"=>$errori,"successi"=>$successi);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dettaglio Libro - Lapiary</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!--Javascript-->
    <script type="text/javascript" src="js/controllo_libro.js"></script>
</head>
<body>
<header class="libri-header">
    <div class="strato1">
        <div class="strato2">
            <h1>Lapiary _ </h1>
            <h2><a href="index.php">Homepage</a> > <a href="libri.php">Lista libri</a> > Dettaglio</h2>
        </div>
    </div>
</header>
<div class="container bcol-libri" id="dettaglio">
    <section class="contents">
        <article class="sopra-form">
            <p class="torna_lista"><a href="libri.php"> < Torna alla lista libri </a></p>
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
            <form class="form form-libro" action="libri_dettaglio.php<?= ($mode == "modifica" && isset($libro_mod)) ? "?mode=modifica&id=".$libro_mod->id : null ?>" name="libro_post" method="post" onsubmit="return ControllaForm(this)">
                <div class="form_title">
                    <h3><?= $mode == "modifica" ? "Modifica" : "Inserisci" ?> i dati</h3>
                </div>
                <div class="form_input">
                    <div class="form-box-sx">
                        <?= (isset($libro_mod)) ? '<input style="visibility:hidden;position:absolute" name="id" Value="'.$libro_mod->id.'"/>' : null?>
                        <label>Titolo</label>
                        <input type="text" class="input" name="titolo" placeholder="es. Il Visconte Dimezzato" <?= (isset($libro_mod)) ? 'Value="'.$libro_mod->titolo.'"' : null?> onblur="ControlloImmediato(this);" required />
                        <label>Autore</label>
                        <input type="text" class="input" name="autore" placeholder="es. Italo Calvino" <?= (isset($libro_mod)) ? 'Value="'.$libro_mod->autore.'"' : null?> onblur="ControlloImmediato(this);" required />
                        <label>Data di pubblicazione</label>
                        <input type="number" min="0" max="2018" class="input" name="data" placeholder="Data di pubblicazione" <?= (isset($libro_mod)) ? 'Value="'.$libro_mod->data.'"' : null?> onblur="ControlloImmediato(this);" required />
                        <label>Genere</label>
                        <input type="text" class="input" name="genere" placeholder="es. Narrativa" <?= (isset($libro_mod)) ? 'Value="'.$libro_mod->genere.'"' : null?> onblur="ControlloImmediato(this);" required />
                    </div>
                    <div class="form-box-dx">

                    </div>
                </div>
                <input type="submit" class="submit submit-libro" name="submit" value="Invia" />
            </form>
        </article>
    </section>
</div>
<footer>
    <p class="piccolino">Lapiary - 2018</p>
</footer>
</body>
</html>