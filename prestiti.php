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
    include_once("models/Prestito.php");
    switch($_GET['mode']){
        case "elimina":
            $mode = "elimina";
            if(isset($_GET['id']) && $_GET['id'] != ""){
                $prestito_by_id = Prestito::getPrestitoByID($_GET['id']);
                if(is_object($prestito_by_id)){
                    $risultato = "";
                    $controllo = $prestito_by_id->controlloPrestito();
                    if ($controllo === true) {
                        $risultato = $prestito_by_id->deletePrestito();
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

                }else{
                    $errori[] = $prestito_by_id[Prestito::ERRORE];
                }
            }
            break;
        default:
            break;
    }
}

// Inglobo negli avvisi gli errori e i successi per mostrarli
$avvisi = array("errori"=>$errori,"successi"=>$successi);

include_once('Segreteria.php');
$segreteria = new Segreteria();
$lista_segreteria = $segreteria->getPrestiti();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Lista Utenti - Lapiary</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/styles.css" />
    </head>
    <body>
        <header class="prestiti-header">
            <div class="strato1">
                <div class="strato2">
                    <h1>Lapiary _ </h1>
                    <h2><a href="index.php">Homepage</a> > Lista prestiti</h2>
                </div>
            </div>
        </header>
        <div class="container" id="lista">
            <section class="contents">
                <article class="sopra-lista">
                    <p class="aggiungi_prestito"><a href="prestiti_dettaglio.php">+ Aggiungi prestito</a></p>
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
                    <div class="prestito-header">
                        <div class="prestito-dati">
                            <div class="intest-img"></div>
                            <p>Libro</p>
                            <p>Utente</p>
                            <p>Data inizio</p>
                            <p>Data fine</p>
                            <p>Data riconsegna</p>
                            <div class="intest-arrow"></div>
                        </div>
                    </div>
                    <?php if(!empty($lista_segreteria)): foreach($lista_segreteria as $prestito):?>
                        <div class="prestito">
                            <div class="prestito-dati" >
                                <img class="prestito-img" src="img/prestiti.png" />
                                <p><?= $prestito->nome_libro ?></p>
                                <p><?= $prestito->nome_utente ?></p>
                                <?php $data_inizio = date("j F Y", strtotime($prestito->data_inizio));
                                $data_fine = date("j F Y", strtotime($prestito->data_fine));
                                if($prestito->data_riconsegna != "0000-00-00"){
                                    $data_riconsegna = date("j F Y", strtotime($prestito->data_riconsegna));
                                }else{
                                    $data_riconsegna = "";
                                }
                                ?>
                                <p><?=  $data_inizio ?></p>
                                <p><?=  $data_fine ?></p>
                                <p><?=  $data_riconsegna ?></p>
                                <a href="prestiti_dettaglio.php?mode=modifica&id=<?= $prestito->id ?>"><img class="edit_img" src="img/edit.png" /></a>
                                <a href="#" onclick="mostra_popup('<?= $prestito->id ?>','<?= $prestito->nome_libro ?>','<?= $prestito->nome_utente ?>','<?= $prestito->data_inizio ?>')"><img class="del_img" src="img/delete.png" /></a>
                            </div>
                            <div class="prestito-info" id="<?= $prestito->id ?>">
                                <h3>Informazioni prestito</h3>
                                <div class="contenitore-inliner">
                                    <div class="contenitore-info">
                                        <div></div>
                                        <div></div>
                                    </div></div>
                            </div>
                            <hr>
                        </div>
                    <?php endforeach; else:?>
                        <div class="no_prestiti">
                            <img src="img/prestiti.png" />
                            <h2>Non c'è ancora nessun prestito registrato</h2>
                            <h3>Comincia subito aggiungendo il primo</h3>
                        </div>
                    <?php endif; ?>
                </article>
            </section>
        </div>
        <footer>
            <p class="piccolino">Lapiary - 2018</p>
        </footer>
        <div class="popup_eliminazione" id="prestito_del_popup">
            <div class="popup_eliminazione-container">
                <p>Sei sicuro di voler eliminare il prestito di<br/>
                    <strong><span id="del_utente"></span></strong><br/>
                    per il libro<br/>
                    <strong><span id="del_libro"></span></strong><br/>
                    iniziata il<br/>
                    <strong><span id="del_datainizio"></span></strong><br/>
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

            function mostra_popup(id,libro,utente,datainizio){
                document.getElementById('prestito_del_popup').classList.add('is-visible');
                document.getElementById('del_libro').innerHTML = libro;
                document.getElementById('del_utente').innerHTML = utente;
                document.getElementById('del_datainizio').innerHTML = datainizio;
                document.getElementById('del_si').href = '?mode=elimina&id='+id;
            }

            function nascondi_popup(){
                document.getElementById('prestito_del_popup').classList.remove('is-visible');
                document.getElementById('del_libro').innerHTML = "";
                document.getElementById('del_utente').innerHTML = "";
                document.getElementById('del_datainizio').innerHTML = "";
                document.getElementById('del_si').href = '#0';
            }
            //-->
        </script>
    </body>
</html>