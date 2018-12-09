<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 18:14
 */
// normalizzo l'indirizzo per l'accesso al file di connessione
while (! file_exists('index.php') )
    chdir('..');

include_once("DbConnection.php");

/**
 * Class Libro
 * classe modello per la gestione dei libri
 */
class Libro{
    public $id;
    public $titolo;
    public $autore;
    public $data;
    public $genere;

    // Costanti di controllo
    const ERRORE = "errore";
    const SUCCESSO = "successo";

    const ERR_NO_ID_LIBRO = 101;
    const ERR_NO_ID_LIBRO_MES = "Spiacente, non è stato trovato nessun libro con questo id.";
    const ERR_INS_LIBRO = 102;
    const ERR_INS_LIBRO_MES = "L'inserimento del nuovo libro ha riscontrato qualche problema";
    const ERR_MOD_LIBRO = 106;
    const ERR_MOD_LIBRO_MES = "La modifica al libro ha riscontrato qualche problema";
    const ERR_DEL_LIBRO = 107;
    const ERR_DEL_LIBRO_MES = "L'eliminazione del libro ha riscontrato qualche problema";
    const ERR_DATI_LIBRO = 104;
    const ERR_DATI_LIBRO_MES = "Uno o più campi obbligatori sono stati omessi o sono errati, ricontrolla";

    const SUCC_INS_LIBRO = 201;
    const SUCC_INS_LIBRO_MES = "Il libro è stato aggiunto alla libreria correttamente";
    const SUCC_MOD_LIBRO = 202;
    const SUCC_MOD_LIBRO_MES = "Il libro è stato modificato correttamente";
    const SUCC_DEL_LIBRO = 203;
    const SUCC_DEL_LIBRO_MES = "Il libro è stato eliminato correttamente";


    /**
     * Libro constructor.
     * @param array $dati
     */
    public function __construct($dati) {
        //
        foreach ($dati as $key=>$item) {
            $dati[$key] = htmlentities($item);
        }
        if(isset($dati['id'])){
            settype($dati['id'], "integer");
            $this->id = $dati['id'];
        }
        $this->titolo = $dati['titolo'];
        $this->autore = $dati['autore'];
        $this->data = $dati['data'];
        $this->genere = $dati['genere'];

    }

    /**
     * controlla che il libro non abbia informazioni mancanti e restituisce errori in caso di mancato successo
     * @return array|bool
     */
    public function controlloLibro(){
        if($this->titolo!="" || $this->autore!="" || $this->data!="" || $this->genere!=""){
            return true;
        }
        return array(Libro::ERRORE => array(Libro::ERR_DATI_LIBRO=>Libro::ERR_DATI_LIBRO_MES));
    }

    /**
     * Recupera il libro dal database dal suo ID
     *
     * @param $id
     * @return array|bool|Libro
     */
    public static function getLibroByID($id){
        // avvio la connessione al Database
        $db = new DbConnection();
        $id = htmlentities($id, ENT_QUOTES);
        settype($id, "integer");
        if(is_int($id)){
            $libro = $db->db_conn->prepare('SELECT * FROM libri WHERE libri.id = ?');
            $libro->bindValue(1, $id, PDO::PARAM_INT);
            $libro->execute();
            $risultato = array();
            foreach($libro->fetchAll(PDO::FETCH_ASSOC) as $dati){
                foreach($dati as $key=>$value){
                    $risultato[$key] = $value;
                }
            }
            if(!empty($risultato)){
                return new Libro($risultato);
            }else{
                return array(Libro::ERRORE => array(Libro::ERR_NO_ID_LIBRO=>Libro::ERR_NO_ID_LIBRO_MES));
            }
        }else{
            return false;
        }
    }

    /**
     * Funzione di inerimento nuovo libro
     *
     * @return array
     */
    public function insertLibro() {
        // avvio la connessione al Database
        $db = new DbConnection();
        // Inserisco il nuovo libro
        $ins_libro = $db->db_conn->prepare('INSERT INTO libri (titolo,autore,data,genere) VALUES (?,?,?,?)');
        $ins_libro->bindValue(1, $this->titolo);
        $ins_libro->bindValue(2, $this->autore);
        $ins_libro->bindValue(3, $this->data);
        $ins_libro->bindValue(4, $this->genere);
        // Controllo il risultato dell'inserimento
        if ($ins_libro->execute()) {
            return array(Libro::SUCCESSO => array(Libro::SUCC_INS_LIBRO => Libro::SUCC_INS_LIBRO_MES));
        } else {
            // in caso di fallimento restituisco l'errore
            return array(Libro::ERRORE => array(Libro::ERR_INS_LIBRO => Libro::ERR_INS_LIBRO_MES));

        }
    }

    /**
     * Funzione di modifica di un libro esistente
     *
     * @return array
     */
    public function updateLibro(){
        // avvio la connessione al Database
        $db = new DbConnection();
        // Inserisco il nuovo libro
        $upd_libro = $db->db_conn->prepare('UPDATE libri SET titolo=?,autore=?,data=?,genere=? WHERE id=?');
        $upd_libro->bindValue(1,$this->titolo);
        $upd_libro->bindValue(2,$this->autore);
        $upd_libro->bindValue(3,$this->data);
        $upd_libro->bindValue(4,$this->genere);
        $upd_libro->bindValue(5,$this->id);
        // Controllo il risultato dell'inserimento
        if($upd_libro->execute()){
            return array(Libro::SUCCESSO => array(Libro::SUCC_MOD_LIBRO=>Libro::SUCC_MOD_LIBRO_MES));
        }else{
            // in caso di fallimento restituisco l'errore
            return array(Libro::ERRORE => array(Libro::ERR_MOD_LIBRO=>Libro::ERR_MOD_LIBRO_MES));

        }
    }

    /**
     * Funzione di delete dei libri
     *
     * @return null
     */
    public function deleteLibro(){
        // avvio la connessione al Database
        $db = new DbConnection();
        // Elimino l'utente
        $del_libro = $db->db_conn->prepare('DELETE FROM libri WHERE libri.id=?');
        $del_libro->bindValue(1,$this->id);
        // Controllo il risultato dell'inserimento
        if($del_libro->execute()){
            return array(Libro::SUCCESSO => array(Libro::SUCC_DEL_LIBRO=>Libro::SUCC_DEL_LIBRO_MES));
        }else{
            // e in caso di fallimento restituisco l'errore;
            return array(Libro::ERRORE => array(Libro::ERR_DEL_LIBRO=>Libro::ERR_DEL_LIBRO_MES));
        }
    }
}