<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Manager {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    function stringInsert($str,$insertstr,$pos)
    {
        $str = substr($str, 0, $pos) . $insertstr . substr($str, $pos);
        return $str;
    }

    function alreadyConsulted($cep) {
        
        $formattedCep = $this->stringInsert($cep, "-", 5);

        $query = $this->db->prepare("SELECT * FROM consultas WHERE cep = :cep LIMIT 1");
        if($query->execute(array(':cep' => $formattedCep))) {
            if($query->rowCount() > 0) {
                return true;
            }
        }
        return false;

    }

    function getCepInfo($cep) {

        $formattedCep = $this->stringInsert($cep, "-", 5);
        
        $query = $this->db->prepare("SELECT * FROM consultas WHERE cep = :cep LIMIT 1");
        if($query->execute(array(':cep' => $formattedCep))) {
            if($query->rowCount() > 0) {
                return $query->fetch(PDO::FETCH_ASSOC);
               }
        }
        return null;

    }

    // cep, logradouro, complemento, bairro, localidade, uf, ibge, gia, ddd, siafi
    function insertCepInfo($data) {
        if(!empty($data)) {
            try {
                $query = $this->db->prepare("INSERT INTO consultas(cep, logradouro, complemento, bairro, localidade, uf, ibge, gia, ddd, siafi)
                VALUES(:cep, :logradouro, :complemento, :bairro, :localidade, :uf, :ibge, :gia, :ddd, :siafi)");
                $query->execute(array(
                    ':cep' => $data['cep'],
                    ':logradouro' => $data['logradouro'],
                    ':complemento' => $data['complemento'],
                    ':bairro' => $data['bairro'],
                    ':localidade' => $data['localidade'],
                    ':uf' => $data['uf'],
                    ':ibge' => $data['ibge'],
                    ':gia' => $data['gia'],
                    ':ddd' => $data['ddd'],
                    ':siafi' => $data['siafi']
                ));
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }
    }

}

?>