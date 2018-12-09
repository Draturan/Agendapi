<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 09/12/2018
 * Time: 17:13
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
    // controllo prima che esista però
    $utente_sel = $utente->getUtenteByID($utente->id);
    if(is_object($utente_sel)){
        /*
         * poichè l'utente deve aggiornare anche i numeri di telefono, che hanno id univoci e che raccolgo dalla
         * funzione getUtenteByID($id), aggiungo queste informazioni all'oggetto da aggiornare.
         */
        $utente->num_telefono[0]["num_id"] = $utente_sel->num_telefono[0]["num_id"];
        $utente->num_telefono[1]["num_id"] = $utente_sel->num_telefono[1]["num_id"];
        $utente->num_telefono[2]["num_id"] = $utente_sel->num_telefono[2]["num_id"];

        $risultato = $utente->updateUtente();
        if(key($risultato) == Utente::SUCCESSO){
            // risposta - 200 Ok
            http_response_code(200);
            // mostro messaggio
            echo json_encode(array("message" => array_shift($risultato[Utente::SUCCESSO])));
        }else{
            // qualcosa è andato storto e riporto la risposta e il messaggio d'errore
            http_response_code(503); // service unavailable
            echo json_encode(array("message" => "E' stato riscontrato un errore: ".array_shift($risultato[Utente::ERRORE])));
        }
    }else{
        // risposta 404 - Not found
        http_response_code(404);

        // mostro messaggio d'errore
        echo json_encode(array("message"=> array_shift($utente_sel[Utente::ERRORE])));
    }

}else{
    // altrimenti avverto l'utente che mancano dei dati
    http_response_code(400); // bad request
    echo json_encode(array("message" => "Impossibile aggiornare l'utente: ".array_shift($controllo[Utente::ERRORE])));
}
