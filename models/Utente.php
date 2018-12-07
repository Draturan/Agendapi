<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 17:56
 */

include_once("DbConnection.php");

/**
 * Class Utente
 * classe modello per la gestione degli utenti
 */
class Utente{
    public $id;
    public $nome;
    public $cognome;
    public $data_di_nascita;
    public $cap;
    public $email;
    public $num_telefono;

    // Costanti di controllo
    const ERRORE = "errore";
    const SUCCESSO = "successo";

    const ERR_NO_ID_UTENTE = 101;
    const ERR_NO_ID_UTENTE_MES = "Spiacente, non è stato trovato nessun utente con questo id.";
    const ERR_INS_UTENTE = 102;
    const ERR_INS_UTENTE_MES = "L'inserimento del nuovo utente ha riscontrato qualche problema";
    const ERR_INS_TELEFONI = 103;
    const ERR_INS_TELEFONI_MES = "L'inserimento dei numeri di telefono ha riscontrato qualche problema";
    const ERR_MOD_UTENTE = 106;
    const ERR_MOD_UTENTE_MES = "La modifica dell'utente ha riscontrato qualche problema";
    const ERR_MOD_TELEFONI = 107;
    const ERR_MOD_TELEFONI_MES = "La modifica dei numeri di telefono ha riscontrato qualche problema";
    const ERR_DEL_UTENTE = 108;
    const ERR_DEL_UTENTE_MES = "L'eliminazione dell'utente ha riscontrato qualche problema";
    const ERR_DEL_TELEFONI = 109;
    const ERR_DEL_TELEFONI_MES = "L'eliminazione dei numeri di telefono ha riscontrato qualche problema";
    const ERR_DATI_UTENTE = 104;
    const ERR_DATI_UTENTE_MES = "Uno o più campi obbligatori sono stati omessi o sono errati, ricontrolla";
    const ERR_EMAIL_UTENTE = 105;
    const ERR_EMAIL_UTENTE_MES = "La tua email non sembra essere corretta, ricontrolla di averla scritta bene";

    const SUCC_INS_UTENTE = 201;
    const SUCC_INS_UTENTE_MES = "L'utente è stato aggiunto alla rubrica correttamente";
    const SUCC_MOD_UTENTE = 202;
    const SUCC_MOD_UTENTE_MES = "L'utente è stato modificato correttamente";
    const SUCC_DEL_UTENTE = 202;
    const SUCC_DEL_UTENTE_MES = "L'utente è stato eliminato correttamente";


    /**
     * Utente constructor.
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
        $this->nome = $dati['nome'];
        $this->cognome = $dati['cognome'];
        $this->data_di_nascita = $dati['datanascita'];
        $this->cap = $dati['cap'];
        $this->email = $dati['email'];
        $this->num_telefono = array(array('tipo'=>$dati['tipo1'], 'telefono'=>$dati['telefono1']),
            array( 'tipo'=>$dati['tipo2'], 'telefono'=>$dati['telefono2']),
            array( 'tipo'=>$dati['tipo3'], 'telefono'=>$dati['telefono3']));
        if(array_key_exists("num_id1",$dati)){
            $this->num_telefono[0]["num_id"] = $dati['num_id1'];
            $this->num_telefono[1]["num_id"] = $dati['num_id2'];
            $this->num_telefono[2]["num_id"] = $dati['num_id3'];
        }


    }

    /**
     * controlla che l'utente non abbia informazioni mancanti e fa un controllo sulla validità dell'email
     * restituendo errori in caso di mancato successo
     * @return array|bool
     */
    public function controlloUtente(){
        if($this->nome!="" || $this->cognome!="" || $this->data_di_nascita!="" || $this->cap!="" || $this->email!=""){
            if($this->controllaEmail($this->email)){
                return true;
            }
            return array(Utente::ERRORE => array(Utente::ERR_EMAIL_UTENTE=>Utente::ERR_EMAIL_UTENTE_MES));
        }
        return array(Utente::ERRORE => array(Utente::ERR_DATI_UTENTE=>Utente::ERR_DATI_UTENTE_MES));
    }

    /**
     * Recupera l'utente dal database dal suo ID
     *
     * @param $id
     * @return array|bool|Utente
     */
    public static function getUtenteByID($id){
        // avvio la connessione al Database
        $db = new DbConnection();
        $id = htmlentities($id, ENT_QUOTES);
        settype($id, "integer");
        if(is_int($id)){
            $utente = $db->db_conn->prepare('SELECT utenti.*, utenti_telefono.id AS num_id, utenti_telefono.fk_utente, utenti_telefono.tipo, utenti_telefono.telefono FROM utenti INNER JOIN utenti_telefono ON utenti.id = ? AND utenti_telefono.fk_utente = ?');
            $utente->bindValue(1, $id, PDO::PARAM_INT);
            $utente->bindValue(2, $id, PDO::PARAM_INT);
            $utente->execute();
            $risultato = array();
            $i=1;
            foreach($utente->fetchAll(PDO::FETCH_ASSOC) as $dati){
                unset($dati["id"]);
                foreach($dati as $key=>$value){
                    if($key=="tipo" || $key=="telefono" || $key=="num_id"){
                        $risultato[$key."$i"] = $value;
                    }elseif($key=="fk_utente") {
                        $risultato["id"] = $value;
                    }else{
                        $risultato[$key] = $value;
                    }
                }
                $i++;
            }
            if(!empty($risultato)){
                return new Utente($risultato);
            }else{
                return array(Utente::ERRORE => array(Utente::ERR_NO_ID_UTENTE=>Utente::ERR_NO_ID_UTENTE_MES));
            }
        }else{
            return false;
        }
    }

    /**
     * Funzione di inserimento nuovo utente
     *
     * @return array
     */
    public function insertUtente() {
        // avvio la connessione al Database
        $db = new DbConnection();
        // Inserisco il nuovo utente
        $ins_cont = $db->db_conn->prepare('INSERT INTO utenti (nome,cognome,datanascita,cap,email) VALUES (?,?,?,?,?)');
        $ins_cont->bindValue(1, $this->nome);
        $ins_cont->bindValue(2, $this->cognome);
        $ins_cont->bindValue(3, $this->data_di_nascita);
        $ins_cont->bindValue(4, $this->cap);
        $ins_cont->bindValue(5, $this->email);
        // Controllo il risultato dell'inserimento
        if ($ins_cont->execute()) {
            // In presenza di numeri di telefono aggiungo anche loro
            if (!empty($this->num_telefono)) {
                $cont_id = $db->db_conn->lastInsertId();
                $ins_tel = $db->db_conn->prepare('INSERT INTO utenti_telefono (fk_utente,tipo,telefono) VALUES (?,?,?)');
                foreach ($this->num_telefono as $numero) {
                    $ins_tel->bindValue(1, $cont_id, PDO::PARAM_INT);
                    $ins_tel->bindValue(2, $numero['tipo']);
                    $ins_tel->bindValue(3, $numero['telefono']);
                    if (!$ins_tel->execute()) {
                        //                        print_r($ins_tel->error_info);
                        return array(Utente::ERRORE => array(Utente::ERR_INS_TELEFONI => Utente::ERR_INS_TELEFONI_MES));
                    }
                }
            }
            return array(Utente::SUCCESSO => array(Utente::SUCC_INS_UTENTE => Utente::SUCC_INS_UTENTE_MES));
        } else {
            // in caso di fallimento restituisco l'errore
            //            print_r($ins_cont->error_info);
            return array(Utente::ERRORE => array(Utente::ERR_INS_UTENTE => Utente::ERR_INS_UTENTE_MES));

        }
    }

    /**
     * Funzione di modifica di un utente esistente e dei suoi numeri di telefono
     *
     * @return array
     */
    public function updateUtente(){
        // avvio la connessione al Database
        $db = new DbConnection();
        // Modifico l'utente
        $upd_cont = $db->db_conn->prepare('UPDATE utenti SET nome=?,cognome=?,datanascita=?,cap=?,email=? WHERE id=?');
        $upd_cont->bindValue(1,$this->nome);
        $upd_cont->bindValue(2,$this->cognome);
        $upd_cont->bindValue(3,$this->data_di_nascita);
        $upd_cont->bindValue(4,$this->cap);
        $upd_cont->bindValue(5,$this->email);
        $upd_cont->bindValue(6,$this->id);
        // Controllo il risultato della query
        if($upd_cont->execute()){
            // In presenza di numeri di telefono modifico anche loro
            if(!empty($this->num_telefono) ){
                $upd_tel = $db->db_conn->prepare('UPDATE utenti_telefono SET tipo=?,telefono=? WHERE id=?');
                foreach($this->num_telefono as $numero){
                    $upd_tel->bindValue(1,$numero['tipo']);
                    $upd_tel->bindValue(2,$numero['telefono']);
                    $upd_tel->bindValue(3,$numero['num_id'], PDO::PARAM_INT);
                    if(!$upd_tel->execute()){
                        //                        print_r($ins_tel->error_info);
                        return array(Utente::ERRORE => array(Utente::ERR_MOD_TELEFONI=>Utente::ERR_MOD_TELEFONI_MES));
                    }
                }
            }
            return array(Utente::SUCCESSO => array(Utente::SUCC_MOD_UTENTE=>Utente::SUCC_MOD_UTENTE_MES));
        }else{
            // in caso di fallimento restituisco l'errore
            //            print_r($ins_cont->error_info);
            return array(Utente::ERRORE => array(Utente::ERR_MOD_UTENTE=>Utente::ERR_MOD_UTENTE_MES));

        }
    }

    /**
     * Funzione di delete degli utenti
     *
     * @return null
     */
    public function deleteUtente(){
        // avvio la connessione al Database
        $db = new DbConnection();
        // Elimino l'utente
        $del_cont = $db->db_conn->prepare('DELETE FROM utenti WHERE utenti.id=?');
        $del_cont->bindValue(1,$this->id);
        // Controllo il risultato dell'inserimento
        if($del_cont->execute()){
            // In presenza di numeri di telefono elimino anche loro
            if(!empty($this->num_telefono) ){
                $del_tel = $db->db_conn->prepare('DELETE FROM utenti_telefono WHERE utenti_telefono.fk_utente=?');
                $del_tel->bindValue(1,$this->id);
                if(!$del_tel->execute()){
                    //                        print_r($ins_tel->error_info);
                    return array(Utente::ERRORE => array(Utente::ERR_DEL_TELEFONI=>Utente::ERR_DEL_TELEFONI_MES));
                }
            }
            return array(Utente::SUCCESSO => array(Utente::SUCC_DEL_UTENTE=>Utente::SUCC_DEL_UTENTE_MES));
        }else{
            // e in caso di fallimento restituisco l'errore
            return array(Utente::ERRORE => array(Utente::ERR_DEL_UTENTE=>Utente::ERR_DEL_UTENTE_MES));

        }
    }

    /**
     * - controllo che ci sia una sola @ nella stringa
     * - controllo la presenza di ulteriori caratteri "pericolosi"
     * - controllo che la forma della string rispetti il pattern dell'email classica
     * @param String $email
     * @return bool check result
     */
    function controllaEmail($email){
        if((($num_at = count(explode( '@', $email )) - 1)!= 1) or
            (strpos($email,';') || strpos($email,',') || strpos($email,' ')) or
            (!preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email))) {
            return false;
        }else{
            return true;
        }
    }
}