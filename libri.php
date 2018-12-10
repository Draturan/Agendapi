<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 16:46
 */

$errori = array();
$successi = array();
$mode = null;

// Controllo presenza di GET
if(isset($_GET['mode'])){
    include_once("models/Libro.php");
    switch($_GET['mode']){
        case "elimina":
            $mode = "elimina";
            if(isset($_GET['id']) && $_GET['id'] != ""){
                $libro_by_id = Libro::getLibroByID($_GET['id']);
                if(is_object($libro_by_id)){
                    $risultato = "";
                    $controllo = $libro_by_id->controlloLibro();
                    if ($controllo === true) {
                        $risultato = $libro_by_id->deleteLibro();
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

                }else{
                    $errori[] = $libro_by_id[Libro::ERRORE];
                }
            }
            break;
        default:
            break;
    }
}

// Inglobo negli avvisi gli errori e i successi per mostrarli
$avvisi = array("errori"=>$errori,"successi"=>$successi);

include_once('Libreria.php');
$libreria = new Libreria();
$lista_libreria = $libreria->getLibreria();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista Libri - Lapiary</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
<header class="libri-header">
    <div class="strato1">
        <div class="strato2">
            <h1>Lapiary _ </h1>
            <h2><a href="index.php">Homepage</a> > Lista libri</h2>
        </div>
    </div>
</header>
<div class="container" id="lista">
    <section class="contents">
        <article class="sopra-lista">
            <p class="aggiungi_libro"><a href="libri_dettaglio.php">+ Aggiungi libro</a></p>
        </article>
        <article class="sopra-form">
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
        <article class="lista-utenti">
            <h2></h2>
            <div class="libro-header">
                <div class="libro-dati">
                    <div class="intest-img"></div>
                    <p>Titolo</p>
                    <p>Autore</p>
                    <p>Data Pubblicazione</p>
                    <p>Tipologia</p>
                    <div class="intest-arrow"></div>
                </div>
            </div>
            <?php if(!empty($lista_libreria)): foreach($lista_libreria as $libro):?>
                <div class="libro">
                    <div class="libro-dati" >
                        <img class="libro-img" src="img/book.png" />
                        <p><?= $libro->titolo ?></p>
                        <p><?= $libro->autore ?></p>
                        <p><?=  $libro->data ?></p>
                        <p><?=  $libro->genere ?></p>
                        <a href="libri_dettaglio.php?mode=modifica&id=<?= $libro->id ?>"><img class="edit_img" src="img/edit.png" /></a>
                        <a href="#" onclick="mostra_popup('<?= $libro->id ?>','<?= $libro->titolo ?>','<?= $libro->autore ?>')"><img class="del_img" src="img/delete.png" /></a>
                    </div>
                    <div class="libro-info" id="<?= $libro->id ?>">
                        <h3>Informazioni libro</h3>
                        <div class="contenitore-inliner">
                            <div class="contenitore-info">
                                <div></div>
                                <div></div>
                            </div></div>
                    </div>
                    <hr>
                </div>
            <?php endforeach; else:?>
                <div class="no_libri">
                    <img src="img/book.png" />
                    <h2>Non c'è ancora nessun libro in rubrica</h2>
                    <h3>Comincia subito aggiungendo il primo</h3>
                </div>
            <?php endif; ?>
        </article>
    </section>
</div>
<footer>
    <p class="piccolino">Lapiary - 2018</p>
</footer>
<div class="popup_eliminazione" id="libro_del_popup">
    <div class="popup_eliminazione-container">
        <p>Sei sicuro di voler eliminare <br/>
            <strong><span id="del_titolo"></span></strong><br/>
            di<br/>
            <strong><span id="del_autore"></span></strong><br/>
            dalla lista?</p>
        <ul class="popup_buttons">
            <li><a href="#" id="del_si">Si</a></li>
            <li><a href="#" onclick="nascondi_popup()">No</a></li>
        </ul>
        <a href="#" class="popup_eliminazione-close img-replace" onclick="nascondi_popup()"></a>
    </div>
</div>
<script type="text/javascript">
    <!--
    function avvia_animazione_info(id){
        document.getElementById('arrow-'+id).classList.toggle('frecciattiva');
        document.getElementById(id.toString()).classList.toggle('updates-info-active');
    }

    function mostra_popup(id,titolo,autore){
        document.getElementById('libro_del_popup').classList.add('is-visible');
        document.getElementById('del_titolo').innerHTML = titolo;
        document.getElementById('del_autore').innerHTML = autore;
        document.getElementById('del_si').href = '?mode=elimina&id='+id;
    }

    function nascondi_popup(){
        document.getElementById('libro_del_popup').classList.remove('is-visible');
        document.getElementById('del_titolo').innerHTML = '';
        document.getElementById('del_autore').innerHTML = '';
        document.getElementById('del_si').href = '#0';
    }
    //-->
</script>
</body>
</html>
