<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 09/12/2018
 * Time: 20:18
 */

// headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// normalizzo l'indirizzo per l'accesso al file di connessione
while (! file_exists('index.php') )
    chdir('..');
include_once("models/Prestito.php");

// raccolgo il dato dal GET se c'è
$id = isset($_GET['id']) ? $_GET['id'] : die();

// recupero l'utente
$prestito_sel = Prestito::getPrestitoByID($id);

if(is_object($prestito_sel)){
    // risposta 200 - OK
    http_response_code(200);

    // mostro i dati in json
    echo json_encode($prestito_sel);
}else{
    // risposta 404 - Not found
    http_response_code(404);

    // mostro messaggio d'errore
    echo json_encode(array("message"=> array_shift($prestito_sel[Prestito::ERRORE])));
}