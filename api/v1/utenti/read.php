<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 08/12/2018
 * Time: 15:59
 */

// headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// normalizzo l'indirizzo per l'accesso al file di connessione
while (! file_exists('index.php') )
    chdir('..');
include_once("Rubrica.php");

$rubrica = new Rubrica();
$lista_utenti = $rubrica->getRubrica();

if(!empty($lista_utenti)){
    // risposta 200 - OK
    http_response_code(200);

    // mostro i dati in json
    echo json_encode($lista_utenti);
}else{
    // risposta 404 - Not found
    http_response_code(404);

    // mostro messaggio d'errore
    echo json_encode(array("message"=> Rubrica::ERR_NO_UTENTI_MES));
}