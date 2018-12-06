<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 15:20
 */


$errori = array();
$successi = array();
$mode = null;

// Controllo presenza di GET
if(isset($_GET['mode'])){
    include_once("models/Utente.php");
    switch($_GET['mode']){
        case "elimina":
            $mode = "elimina";
            if(isset($_GET['id']) && $_GET['id'] != ""){
                $utente_by_id = Utente::getUtenteByID($_GET['id']);
                if(is_object($utente_by_id)){
                    $risultato = "";
                    $controllo = $utente_by_id->controlloUtente();
                    if ($controllo === true) {
                        $risultato = $utente_by_id->deleteUtente();
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

                }else{
                    $errori[] = $utente_by_id[Utente::ERRORE];
                }
            }
            break;
        default:
            break;
    }
}

// Inglobo negli avvisi gli errori e i successi per mostrarli
$avvisi = array("errori"=>$errori,"successi"=>$successi);

include_once('Rubrica.php');
$rubrica = new Rubrica();
$lista_rubrica = $rubrica->getRubrica();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista Utenti - Lapiary</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
    <header>
        <div class="strato1">
            <div class="strato2">
                <h1>Lapiary _ </h1>
                <h2><a href="index.php">Homepage</a> > Lista utenti</h2>
            </div>
        </div>
    </header>
    <div class="container" id="lista">
        <section class="contents">
            <article class="sopra-lista">
                <p class="aggiungi_utente"><a href="utenti_dettaglio.php">+ Aggiungi utente</a></p>
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
                <div class="utente-header">
                    <div class="utente-dati">
                        <div class="intest-img"></div>
                        <p>Nome e Cognome</p>
                        <p>Email</p>
                        <p>Numero Principale</p>
                        <div class="intest-arrow"></div>
                    </div>
                </div>
                <?php if(!empty($lista_rubrica)): foreach($lista_rubrica as $utente):?>
                    <div class="utente">
                        <div class="utente-dati" onclick="avvia_animazione_info('<?= $utente->id ?>');">
                            <img class="utente-img" src="img/utente.png" />
                            <p><?= $utente->nome." ".$utente->cognome ?></p>
                            <p><?= $utente->email ?></p>
                            <p><?=  $utente->num_telefono[0]['tipo'] != "" ? $utente->num_telefono[0]['tipo']." | ".$utente->num_telefono[0]['telefono'] : "" ?></p>
                            <img class="utente-arrow" id="arrow-<?= $utente->id ?>" src="img/arrow-down.png" />
                        </div>
                        <div class="utente-info" id="<?= $utente->id ?>">
                            <h3>Informazioni utente</h3>
                            <div class="contenitore-inliner">
                                <div class="contenitore-info">
                                    <div>
                                        <?php $data = date("j F Y", strtotime($utente->data_di_nascita)) ?>
                                        <p><?= "Data di nascita: ".$data ?></p>
                                        <p><?= "Cap: ".$utente->cap ?></p>
                                    </div>
                                    <div>
                                        <p><?= $utente->num_telefono[1]['tipo'] != "" ? "Telefono #2: ".$utente->num_telefono[1]['tipo']." | ".$utente->num_telefono[1]['telefono'] : "" ?></p>
                                        <p><?=  $utente->num_telefono[2]['tipo'] != "" ? "Telefono #3: ".$utente->num_telefono[2]['tipo']." | ".$utente->num_telefono[2]['telefono'] : "" ?></p>
                                    </div>
                                </div>
                                <a href="utenti_dettaglio.php?mode=modifica&id=<?= $utente->id ?>"><img class="edit_img" src="img/edit.png" /></a>
                                <a href="#" onclick="mostra_popup('<?= $utente->id ?>','<?= $utente->nome ?>','<?= $utente->cognome ?>')"><img class="del_img" src="img/delete.png" /></a>
                            </div>
                        </div>
                        <hr>
                    </div>
                <?php endforeach; else:?>
                    <div class="no_utenti">
                        <img src="img/address_book.png" />
                        <h2>Non c'è ancora nessun utente in rubrica</h2>
                        <h3>Comincia subito aggiungendo il primo</h3>
                    </div>
                <?php endif; ?>
            </article>
        </section>
    </div>
    <footer>
        <p class="piccolino">Lapiary - 2018</p>
    </footer>
    <div class="popup_eliminazione" id="utente_del_popup">
        <div class="popup_eliminazione-container">
            <p>Sei sicuro di voler eliminare <br/>
                <strong><span id="del_nomecogn"></span></strong><br/>
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

        function mostra_popup(id,nome,cognome){
            document.getElementById('utente_del_popup').classList.add('is-visible');
            document.getElementById('del_nomecogn').innerHTML = nome+' '+cognome;
            document.getElementById('del_si').href = '?mode=elimina&id='+id;
        }

        function nascondi_popup(){
            document.getElementById('utente_del_popup').classList.remove('is-visible');
            document.getElementById('del_nomecogn').innerHTML = '';
            document.getElementById('del_si').href = '#0';
        }
        //-->
    </script>
</body>
</html>
