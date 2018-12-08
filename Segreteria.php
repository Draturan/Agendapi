<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 07/12/2018
 * Time: 14:48
 */

include_once("DbConnection.php");
include_once("models/Prestito.php");
include_once("Libreria.php");
include_once("Rubrica.php");

/**
 * Class Segreteria
 * per la gestione dei prestiti
 */
class Segreteria{

    private $db;

    public function __construct() {
        $this->db = new DbConnection();
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
        $query = "SELECT prestiti.*, libri.titolo, utenti.nome, utenti.cognome
                  FROM libri
                  INNER JOIN prestiti
                  ON prestiti.fk_libro = libri.id
                  INNER JOIN utenti
                  ON prestiti.fk_utente=utenti.id ORDER BY data_riconsegna ASC";
        if($limit != null){
            $segreteria = $this->db->db_conn->prepare($query);
            $segreteria->bindValue(1, $limit, PDO::PARAM_INT);
        }else{
            $segreteria = $this->db->db_conn->prepare($query);
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

    /**
     * Restituisce i Libri che sono attualmente in prestito
     *
     * @return array[Prestito Objects]
     */
    public function getLibriInPrestito(){
        $query = "SELECT prestiti.* , libri.titolo , utenti.nome, utenti.cognome
                    FROM libri INNER JOIN prestiti
                    ON prestiti.fk_libro = libri.id INNER JOIN utenti
                    ON prestiti.fk_utente = utenti.id WHERE prestiti.data_riconsegna = '0000-00-00'";
        $segreteria = $this->db->db_conn->prepare($query);
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

    public function getLibriDisponibili(){
        // Prendo la lista dei libri presenti
        $libri_obj = new Libreria();
        $lista_libri = $libri_obj->getLibreria();
        // ora quelli presi in prestito
        $lista_prestiti = $this->getLibriInPrestito();
        // se ci sono prestiti faccio la differenza
        if(!empty($lista_prestiti)){
            $da_eliminare = array();
            foreach($lista_prestiti as $prestito){
                $da_eliminare[$prestito->fk_libro] = $prestito->nome_libro;
            }
            // li sottraggo alla lista completa ottenendo quelli disponibili
            $lista_disponibili = array_diff_key($lista_libri,$da_eliminare);
            return $lista_disponibili;
        }else{
            // altrimenti invio la lista completa
            return $lista_libri;
        }


    }
}