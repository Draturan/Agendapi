<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 18:06
 */

include_once("DbConnection.php");
include_once("models/Utente.php");

/**
 * Class Rubrica
 * per la gestione della rubrica
 */
class Rubrica{

    public function __construct() {

    }

    /**
     * Funzione per recuperare l'intera rubrica dal database
     * il valore limit può essere utilizzato per limitare la visualizzazione dei utenti
     * in una sola pagina.
     *
     * @param null $limit
     * @return array
     */
    public function getRubrica($limit=null){
        $db = new DbConnection();
        if($limit != null){
            $rubrica = $db->db_conn->prepare('SELECT utenti.*, utenti_telefono.id
                                              AS num_id, utenti_telefono.fk_utente, utenti_telefono.tipo, utenti_telefono.telefono
                                              FROM  utenti
                                              LEFT JOIN utenti_telefono
                                              ON utenti_telefono.fk_utente = utenti.id LIMIT ?');
            $rubrica->bindValue(1, $limit, PDO::PARAM_INT);
        }else{
            $rubrica = $db->db_conn->prepare('SELECT utenti.*, utenti_telefono.id
                                              AS num_id, utenti_telefono.fk_utente, utenti_telefono.tipo, utenti_telefono.telefono
                                              FROM  utenti
                                              LEFT JOIN utenti_telefono
                                              ON utenti_telefono.fk_utente = utenti.id');
        }
        $rubrica->execute();
        $risultato = array();
        $i=1;
        foreach($rubrica->fetchAll(PDO::FETCH_ASSOC) as $utente){
            unset($utente["id"]);
            foreach($utente as $key=>$value){
                if(array_key_exists($utente["fk_utente"],$risultato)){
                    if($key=="tipo" || $key=="telefono" || $key=="num_id"){
                        $risultato[$utente["fk_utente"]][$key."$i"] = $value;
                    }elseif($key=="fk_utente") {
                        $risultato[$utente["fk_utente"]]["id"] = $value;
                    }else{
                        $risultato[$utente["fk_utente"]][$key] = $value;
                    }
                }else{
                    $risultato[$utente["fk_utente"]][$key] = $value;
                    $i=1;
                }
            }
            $i++;
        }
        // Ordinati i risultati genero un array di oggetti Utente da restituire
        $lista_utenti = array();
        foreach ($risultato as $key=>$obj_array) {
            $lista_utenti[$key] = new Utente($obj_array);
        }
        return $lista_utenti;
    }
}