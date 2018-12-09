<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 09/12/2018
 * Time: 23:50
 */


// headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// normalizzo l'indirizzo per l'accesso al file di connessione
while (! file_exists('index.php') )
    chdir('..');
include_once("Segreteria.php");

$segreteria = new Segreteria();
$lista_prest = $segreteria->getLibriDisponibili();

if(!empty($lista_prest)){
    // risposta 200 - OK
    http_response_code(200);

    // mostro i dati in json
    echo json_encode($lista_prest);
}else{
    // risposta 404 - Not found
    http_response_code(404);

    // mostro messaggio d'errore
    echo json_encode(array("message"=> Segreteria::ERR_NO_PRESTITI_MES));
}