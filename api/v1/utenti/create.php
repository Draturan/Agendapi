<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 08/12/2018
 * Time: 17:53
 */
// headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// normalizzo l'indirizzo per l'accesso al file di connessione
while (! file_exists('index.php') )
    chdir('..');
include_once("models/Utente.php");

// prendo il post e raccolgo i dati
$data = json_decode(file_get_contents("php://input"));

// controllo che i dati siano stati forniti
$controllo = Utente::controlloUtente($data);
if($controllo === true){
    // creo l'oggetto ed eseguo l'inserimento
    $utente = new Utente($data);
    $risultato = $utente->insertUtente();
    if(key($risultato) == Utente::SUCCESSO){
        // risposta - 201 created
        http_response_code(201);
        // mostro messaggio
        echo json_encode(array("message" => Utente::SUCC_INS_UTENTE_MES));
    }else{
        // qualcosa è andato storto e riporto la risposta e il messaggio d'errore
        http_response_code(503); // service unavailable
        echo json_encode(array("message" => "E' stato riscontrato un errore: ".array_shift($risultato[Utente::ERRORE])));
    }
}else{
    // altrimenti avverto l'utente che mancano dei dati
    http_response_code(400); // bad request
    echo json_encode(array("message" => "Impossibile creare l'utente: ".array_shift($controllo[Utente::ERRORE])));
}
