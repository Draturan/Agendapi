<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 07/12/2018
 * Time: 14:47
 */

include_once("DbConnection.php");

/**
 * Class Prestito
 * classe modello per la gestione dei libri
 */
class Prestito{
    public $id;
    public $fk_libro;
    public $nome_libro;
    public $fk_utente;
    public $nome_utente;
    public $data_inizio;
    public $data_fine;
    public $data_riconsegna;

    // Costanti di controllo
    const ERRORE = "errore";
    const SUCCESSO = "successo";

    const ERR_NO_ID_PREST = 101;
    const ERR_NO_ID_PREST_MES = "Spiacente, non è stato trovato nessun prestito con questo id.";
    const ERR_INS_PREST = 102;
    const ERR_INS_PREST_MES = "L'inserimento del nuovo prestito ha riscontrato qualche problema";
    const ERR_MOD_PREST = 106;
    const ERR_MOD_PREST_MES = "La modifica al prestito ha riscontrato qualche problema";
    const ERR_DEL_PREST = 107;
    const ERR_DEL_PREST_MES = "L'eliminazione del prestito ha riscontrato qualche problema";
    const ERR_DATI_PREST = 104;
    const ERR_DATI_PREST_MES = "Uno o più campi obbligatori sono stati omessi o sono errati, ricontrolla";

    const SUCC_INS_PREST = 201;
    const SUCC_INS_PREST_MES = "Il prestito è stato aggiunto alla libreria correttamente";
    const SUCC_MOD_PREST = 202;
    const SUCC_MOD_PREST_MES = "Il prestito è stato modificato correttamente";
    const SUCC_DEL_PREST = 202;
    const SUCC_DEL_PREST_MES = "Il prestito è stato eliminato correttamente";


    /**
     * Prestito constructor.
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
        $this->fk_libro = $dati['fk_libro'];
        $this->nome_libro = $dati['titolo'];
        $this->fk_utente = $dati['fk_utente'];
        $this->nome_utente = $dati['nome']." ".$dati['cognome'];
        $this->data_inizio = $dati['data_inizio'];
        $this->data_fine = $dati['data_fine'];
        $this->data_riconsegna = $dati['data_riconsegna'];
    }

    /**
     * controlla che il prestito non abbia informazioni mancanti e restituisce errori in caso di mancato successo
     * @return array|bool
     */
    public function controlloPrestito(){
        if($this->fk_libro!="" || $this->fk_utente!="" || $this->data_inizio!="" || $this->data_fine!=""){
            return true;
        }
        return array(Prestito::ERRORE => array(Prestito::ERR_DATI_PREST=>Prestito::ERR_DATI_PREST_MES));
    }

    /**
     * Recupera il prestito dal database dal suo ID
     *
     * @param $id
     * @return array|bool|Prestito
     */
    public static function getPrestitoByID($id){
        // avvio la connessione al Database
        $db = new DbConnection();
        $id = htmlentities($id, ENT_QUOTES);
        settype($id, "integer");
        if(is_int($id)){
            $prestito = $db->db_conn->prepare('SELECT * FROM prestiti WHERE prestiti.id = ?');
            $prestito->bindValue(1, $id, PDO::PARAM_INT);
            $prestito->execute();
            $risultato = array();
            foreach($prestito->fetchAll(PDO::FETCH_ASSOC) as $dati){
                foreach($dati as $key=>$value){
                    $risultato[$key] = $value;
                }
            }
            if(!empty($risultato)){
                return new Prestito($risultato);
            }else{
                return array(Prestito::ERRORE => array(Prestito::ERR_NO_ID_PREST=>Prestito::ERR_NO_ID_PREST_MES));
            }
        }else{
            return false;
        }
    }

    /**
     * Funzione di inerimento nuovo prestito
     *
     * @return array
     */
    public function insertPrestito() {
        // avvio la connessione al Database
        $db = new DbConnection();
        // Inserisco il nuovo prestito
        $ins_prestito = $db->db_conn->prepare('INSERT INTO libri (titolo,autore,data,genere) VALUES (?,?,?,?)');
        $ins_prestito->bindValue(1, $this->titolo);
        $ins_prestito->bindValue(2, $this->autore);
        $ins_prestito->bindValue(3, $this->data);
        $ins_prestito->bindValue(4, $this->genere);
        // Controllo il risultato dell'inserimento
        if ($ins_prestito->execute()) {
            return array(Prestito::SUCCESSO => array(Prestito::SUCC_INS_PREST => Prestito::SUCC_INS_PREST_MES));
        } else {
            // in caso di fallimento restituisco l'errore
            //            print_r($ins_prestito->error_info);
            return array(Prestito::ERRORE => array(Prestito::ERR_INS_PREST => Prestito::ERR_INS_PREST_MES));

        }
    }

    /**
     * Funzione di modifica di un prestito esistente e dei suoi numeri di telefono
     *
     * @return array
     */
    public function updatePrestito(){
        // avvio la connessione al Database
        $db = new DbConnection();
        // Inserisco il nuovo prestito
        $upd_prestito = $db->db_conn->prepare('UPDATE libri SET titolo=?,autore=?,data=?,genere=? WHERE id=?');
        $upd_prestito->bindValue(1,$this->titolo);
        $upd_prestito->bindValue(2,$this->autore);
        $upd_prestito->bindValue(3,$this->data);
        $upd_prestito->bindValue(4,$this->genere);
        $upd_prestito->bindValue(6,$this->id);
        // Controllo il risultato dell'inserimento
        if($upd_prestito->execute()){
            return array(Prestito::SUCCESSO => array(Prestito::SUCC_MOD_PREST=>Prestito::SUCC_MOD_PREST_MES));
        }else{
            // in caso di fallimento restituisco l'errore
            //            print_r($ins_prestito->error_info);
            return array(Prestito::ERRORE => array(Prestito::ERR_MOD_PREST=>Prestito::ERR_MOD_PREST_MES));

        }
    }

    /**
     * Funzione di delete dei libri
     *
     * @return null
     */
    public function deletePrestito(){
        // avvio la connessione al Database
        $db = new DbConnection();
        // Elimino l'utente
        $del_prestito = $db->db_conn->prepare('DELETE FROM libri WHERE libri.id=?');
        $del_prestito->bindValue(1,$this->id);
        // Controllo il risultato dell'inserimento
        if($del_prestito->execute()){
            return array(Prestito::SUCCESSO => array(Prestito::SUCC_DEL_PREST=>Prestito::SUCC_DEL_PREST_MES));
        }else{
            // e in caso di fallimento restituisco l'errore;
            return array(Prestito::ERRORE => array(Prestito::ERR_DEL_PREST=>Prestito::ERR_DEL_PREST_MES));
        }
    }
}