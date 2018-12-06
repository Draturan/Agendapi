<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 17:54
 */


class DbConnection{

    // Inizializzo le variabili per la connessione
    private $conn_config = array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'miningful_lapiary',
        'charset' => 'utf8mb4',
        'username' => 'root',
        'password' => '',
        'options' => array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false )
    );
    public $db_conn;

    /**
     * DbConnection constructor.
     */
    public function __construct(){
        //alla creazione dell'oggetto faccio partire la connessione
        $this->getPDOConnection();
    }

    /**
     * Funzione di connesione al database
     */
    private function getPDOConnection() {
        // verifico che l'istanza non sia già stata avviata
        if($this->db_conn == NULL){
            // creo la connessione
            $dsn = "".$this->conn_config['driver']
                .":host=".$this->conn_config['host']
                .";dbname=".$this->conn_config['dbname']
                .";charset=".$this->conn_config['charset'];
            try{
                $this->db_conn = new PDO($dsn,$this->conn_config['username'],$this->conn_config['password'],
                    $this->conn_config['options']);
            }catch (PDOException $e){
                print($e->getMessage());
            }
        }
    }

}