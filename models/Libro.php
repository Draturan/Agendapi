<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 18:14
 */

include_once("DbConnection.php");

/**
 * Class Libro
 * classe modello per la gestione dei utenti
 */
class Libro{
    public $id;
    public $titolo;
    public $autore;
    public $data;
    public $tipologia;

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
    const ERR_DATI_LIBRO_MES = "Uno o più campi obbligatori è stato omesso o è errato, ricontrolla";

    const SUCC_INS_LIBRO = 201;
    const SUCC_INS_LIBRO_MES = "Il libro è stato aggiunto alla rubrica correttamente";
    const SUCC_MOD_LIBRO = 202;
    const SUCC_MOD_LIBRO_MES = "Il libro è stato modificato correttamente";
    const SUCC_DEL_LIBRO = 202;
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
     * controlla che il libro non abbia informazioni mancanti e fa un controllo sulla validità dell'email
     * restituendo errori in caso di mancato successo
     * @return array|bool
     */
    public function controlloLibro(){
        if($this->nome!="" || $this->cognome!="" || $this->data_di_nascita!="" || $this->cap!="" || $this->email!=""){
            if($this->controllaEmail($this->email)){
                return true;
            }
            return array(Libro::ERRORE => array(Libro::ERR_EMAIL_LIBRO=>Libro::ERR_EMAIL_LIBRO_MES));
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
            $libro = $db->db_conn->prepare('SELECT utenti.*, numeri_telefono.id AS num_id, numeri_telefono.fk_libro, numeri_telefono.tipo, numeri_telefono.telefono FROM utenti INNER JOIN numeri_telefono ON utenti.id = ? AND numeri_telefono.fk_libro = ?');
            $libro->bindValue(1, $id, PDO::PARAM_INT);
            $libro->bindValue(2, $id, PDO::PARAM_INT);
            $libro->execute();
            $risultato = array();
            $i=1;
            foreach($libro->fetchAll(PDO::FETCH_ASSOC) as $dati){
                unset($dati["id"]);
                foreach($dati as $key=>$value){
                    if($key=="tipo" || $key=="telefono" || $key=="num_id"){
                        $risultato[$key."$i"] = $value;
                    }elseif($key=="fk_libro") {
                        $risultato["id"] = $value;
                    }else{
                        $risultato[$key] = $value;
                    }
                }
                $i++;
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
                $ins_tel = $db->db_conn->prepare('INSERT INTO numeri_telefono (fk_libro,tipo,telefono) VALUES (?,?,?)');
                foreach ($this->num_telefono as $numero) {
                    $ins_tel->bindValue(1, $cont_id, PDO::PARAM_INT);
                    $ins_tel->bindValue(2, $numero['tipo']);
                    $ins_tel->bindValue(3, $numero['telefono']);
                    if (!$ins_tel->execute()) {
                        //                        print_r($ins_tel->error_info);
                        return array(Libro::ERRORE => array(Libro::ERR_INS_TELEFONI => Libro::ERR_INS_TELEFONI_MES));
                    }
                }
            }
            return array(Libro::SUCCESSO => array(Libro::SUCC_INS_LIBRO => Libro::SUCC_INS_LIBRO_MES));
        } else {
            // in caso di fallimento restituisco l'errore
            //            print_r($ins_cont->error_info);
            return array(Libro::ERRORE => array(Libro::ERR_INS_LIBRO => Libro::ERR_INS_LIBRO_MES));

        }
    }

    /**
     * Funzione di modifica di un libro esistente e dei suoi numeri di telefono
     *
     * @return array
     */
    public function updateLibro(){
        // avvio la connessione al Database
        $db = new DbConnection();
        // Inserisco il nuovo libro
        $upd_cont = $db->db_conn->prepare('UPDATE utenti SET nome=?,cognome=?,datanascita=?,cap=?,email=? WHERE id=?');
        $upd_cont->bindValue(1,$this->nome);
        $upd_cont->bindValue(2,$this->cognome);
        $upd_cont->bindValue(3,$this->data_di_nascita);
        $upd_cont->bindValue(4,$this->cap);
        $upd_cont->bindValue(5,$this->email);
        $upd_cont->bindValue(6,$this->id);
        // Controllo il risultato dell'inserimento
        if($upd_cont->execute()){
            // In presenza di numeri di telefono aggiungo anche loro
            if(!empty($this->num_telefono) ){
                $upd_tel = $db->db_conn->prepare('UPDATE numeri_telefono SET tipo=?,telefono=? WHERE id=?');
                foreach($this->num_telefono as $numero){
                    $upd_tel->bindValue(1,$numero['tipo']);
                    $upd_tel->bindValue(2,$numero['telefono']);
                    $upd_tel->bindValue(3,$numero['num_id'], PDO::PARAM_INT);
                    if(!$upd_tel->execute()){
                        //                        print_r($ins_tel->error_info);
                        return array(Libro::ERRORE => array(Libro::ERR_MOD_TELEFONI=>Libro::ERR_MOD_TELEFONI_MES));
                    }
                }
            }
            return array(Libro::SUCCESSO => array(Libro::SUCC_MOD_LIBRO=>Libro::SUCC_MOD_LIBRO_MES));
        }else{
            // in caso di fallimento restituisco l'errore
            //            print_r($ins_cont->error_info);
            return array(Libro::ERRORE => array(Libro::ERR_MOD_LIBRO=>Libro::ERR_MOD_LIBRO_MES));

        }
    }

    /**
     * Funzione di delete dei utenti non implementata
     *
     * @return null
     */
    public function deleteLibro(){
        return null;
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