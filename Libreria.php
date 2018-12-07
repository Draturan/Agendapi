<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 18:14
 */

include_once("DbConnection.php");
include_once("models/Libro.php");

/**
 * Class Libreria
 * per la gestione dei libri
 */
class Libreria{

    public function __construct() {

    }

    /**
     * Funzione per recuperare l'intera libreria dal database
     * il valore limit può essere utilizzato per limitare la visualizzazione dei libri
     * in una sola pagina.
     *
     * @param null $limit
     * @return array
     */
    public function getLibreria($limit=null){
        $db = new DbConnection();
        if($limit != null){
            $libreria = $db->db_conn->prepare('SELECT * FROM libri');
            $libreria->bindValue(1, $limit, PDO::PARAM_INT);
        }else{
            $libreria = $db->db_conn->prepare('SELECT * FROM libri');
        }
        $libreria->execute();
        $risultato = array();
        foreach($libreria->fetchAll(PDO::FETCH_ASSOC) as $dati){
            foreach($dati as $key=>$value){
                $risultato[$dati['id']][$key] = $value;
            }
        }
        // Ordinati i risultati genero un array di oggetti Libro da restituire
        $lista_libri = array();
        foreach ($risultato as $key=>$obj_array) {
            $lista_libri[] = new Libro($obj_array);
        }
        return $lista_libri;
    }
}