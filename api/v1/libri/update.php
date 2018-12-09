<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 09/12/2018
 * Time: 19:54
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
include_once("models/Libro.php");

// prendo il post e raccolgo i dati
$data = json_decode(file_get_contents("php://input"));
// controllo che i dati siano stati forniti
$controllo = Libro::controlloLibro($data);
if($controllo === true){
    // creo l'oggetto ed eseguo l'inserimento
    $libro = new Libro($data);
    // controllo prima che esista però
    $libro_sel = $libro->getLibroByID($libro->id);
    if(is_object($libro_sel)){
        $risultato = $libro->updateLibro();
        if(key($risultato) == Libro::SUCCESSO){
            // risposta - 200 Ok
            http_response_code(200);
            // mostro messaggio
            echo json_encode(array("message" => array_shift($risultato[Libro::SUCCESSO])));
        }else{
            // qualcosa è andato storto e riporto la risposta e il messaggio d'errore
            http_response_code(503); // service unavailable
            echo json_encode(array("message" => "E' stato riscontrato un errore: ".array_shift($risultato[Libro::ERRORE])));
        }
    }else{
        // risposta 404 - Not found
        http_response_code(404);

        // mostro messaggio d'errore
        echo json_encode(array("message"=> array_shift($libro_sel[Libro::ERRORE])));
    }

}else{
    // altrimenti avverto l'utente che mancano dei dati
    http_response_code(400); // bad request
    echo json_encode(array("message" => "Impossibile aggiornare il libro: ".array_shift($controllo[Libro::ERRORE])));
}
