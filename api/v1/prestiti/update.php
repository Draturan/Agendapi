<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 09/12/2018
 * Time: 20:32
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
include_once("models/Prestito.php");
include_once("models/Libro.php");
include_once("models/Utente.php");

// prendo il post e raccolgo i dati
$data = json_decode(file_get_contents("php://input"));
// controllo che i dati siano stati forniti
$controllo = Prestito::controlloPrestito($data);
if($controllo === true){
    // creo l'oggetto ed eseguo l'inserimento
    $prestito = new Prestito($data);
    // controllo prima che esista però
    $prestito_sel = Prestito::getPrestitoByID($prestito->id);
    if(is_object($prestito_sel)){
        // controllo che l'id libro esista
        $libro_sel = Libro::getLibroByID($prestito->fk_libro);
        if(is_object($libro_sel)){
            // controllo che l'id utente esista
            $utente_sel = Utente::getUtenteByID($prestito->fk_utente);
            if(is_object($utente_sel)){
                $risultato = $prestito->updatePrestito();
                if(key($risultato) == Prestito::SUCCESSO){
                    // risposta - 200 Ok
                    http_response_code(200);
                    // mostro messaggio
                    echo json_encode(array("message" => array_shift($risultato[Prestito::SUCCESSO])));
                }else{
                    // qualcosa è andato storto e riporto la risposta e il messaggio d'errore
                    http_response_code(503); // service unavailable
                    echo json_encode(array("message" => "E' stato riscontrato un errore: ".array_shift($risultato[Prestito::ERRORE])));
                }
            }else{
                // risposta 404 - Not found
                http_response_code(404);

                // mostro messaggio d'errore
                echo json_encode(array("message"=> array_shift($utente_sel[Utente::ERRORE])));
            }
        }else{
            // risposta 404 - Not found
            http_response_code(404);

            // mostro messaggio d'errore
            echo json_encode(array("message"=> array_shift($libro_sel[Libro::ERRORE])));
        }
    }else{
        // risposta 404 - Not found
        http_response_code(404);

        // mostro messaggio d'errore
        echo json_encode(array("message"=> array_shift($prestito_sel[Prestito::ERRORE])));
    }

}else{
    // altrimenti avverto l'utente che mancano dei dati
    http_response_code(400); // bad request
    echo json_encode(array("message" => "Impossibile aggiornare la prenotazione: ".array_shift($controllo[Prestito::ERRORE])));
}