<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 07/12/2018
 * Time: 14:48
 */

include_once("DbConnection.php");
include_once("models/Prestito.php");

/**
 * Class Segreteria
 * per la gestione dei prestiti
 */
class Segreteria{

    public function __construct() {

    }

    /**
     * Funzione per recuperare l'intera lista prestiti dal database
     * il valore limit può essere utilizzato per limitare la visualizzazione dei prestiti
     * in una sola pagina.
     *
     * @param null $limit
     * @return array
     */
    public function getPrestiti($limit=null){
        $db = new DbConnection();
        $query = "SELECT prestiti.*, libri.titolo, utenti.nome, utenti.cognome
                  FROM libri
                  INNER JOIN prestiti
                  ON prestiti.fk_libro = libri.id
                  INNER JOIN utenti
                  ON prestiti.fk_utente=utenti.id ORDER BY data_riconsegna ASC";
        if($limit != null){
            $segreteria = $db->db_conn->prepare($query);
            $segreteria->bindValue(1, $limit, PDO::PARAM_INT);
        }else{
            $segreteria = $db->db_conn->prepare($query);
        }
        $segreteria->execute();
        $risultato = array();
        foreach($segreteria->fetchAll(PDO::FETCH_ASSOC) as $dati){
            $nome_utente = "";
            foreach($dati as $key=>$value){
                $risultato[$dati['id']][$key] = $value;
            }
        }
        // Ordinati i risultati genero un array di oggetti Prestito da restituire
        $lista_prestiti = array();
        foreach ($risultato as $key=>$obj_array) {
            $lista_prestiti[] = new Prestito($obj_array);
        }
        return $lista_prestiti;
    }

    public function getLibriInPrestito(){
        $db = new DbConnection();
        $query = "SELECT prestiti.* , libri.titolo , utenti.nome, utenti.cognome
                    FROM libri INNER JOIN prestiti
                    ON prestiti.fk_libro = libri.id INNER JOIN utenti
                    ON prestiti.fk_utente = utenti.id WHERE prestiti.data_riconsegna = '0000-00-00'";
        $segreteria = $db->db_conn->prepare($query);
        $segreteria->execute();
        $risultato = array();
        foreach($segreteria->fetchAll(PDO::FETCH_ASSOC) as $dati){
            $nome_utente = "";
            foreach($dati as $key=>$value){
                $risultato[$dati['id']][$key] = $value;
            }
        }
        print_r($risultato);
        // Ordinati i risultati genero un array di oggetti Prestito da restituire
        $lista_prestiti = array();
        foreach ($risultato as $key=>$obj_array) {
            $lista_prestiti[] = new Prestito($obj_array);
        }
        return $lista_prestiti;
    }

    public function getLibriDisponibili(){

        return $this->getLibriInPrestito();;
    }
}