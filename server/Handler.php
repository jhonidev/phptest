<?php

require('Database.php');
require('Manager.php');

$db = new MySQL();
$manager = new Manager($db->getDb());

function simpleXmlToArray($xmlObject)
{
    $array = [];
    foreach ($xmlObject->children() as $node) {
        $array[$node->getName()] = is_array($node) ? simplexml_to_array($node) : (string) $node;
    }

    return $array;
}

if(isset($_POST['cep'])) {

    $cep = $_POST['cep'];
    $formattedCep = str_replace(['.','-'], '', $cep);

    if (preg_match("/^\d+$/", $formattedCep)) {
        if(strlen($formattedCep) == 8) {
            
            if($manager->alreadyConsulted($formattedCep)) {
                http_response_code(200);
                $cepdata = $manager->getCepInfo($formattedCep);
                $response = array(
                    "cep" => $cepdata['cep'],
                    "logradouro" => $cepdata['logradouro'],
                    "complemento" => $cepdata['complemento'],
                    "bairro" => $cepdata['bairro'],
                    "localidade" => $cepdata['localidade'],
                    "uf" => $cepdata['uf'],
                    "ibge" => $cepdata['ibge'],
                    "gia" => $cepdata['gia'],
                    "ddd" => $cepdata['ddd'],
                    "siafi" => $cepdata['siafi']
                );
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                echo $json;
                exit();
            } else {
                $url = "https://viacep.com.br/ws/".$formattedCep."/xml/";
                $xml = file_get_contents($url);
                $data = new SimpleXMLElement($xml);
                $content = simpleXmlToArray($data);
                if(array_key_exists("erro", $content)) {
                    http_response_code(400);
                    $response = array(
                        "response" => 'CEP Inválido'
                    );
                    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json;
                    exit();
                } else {
                    http_response_code(200);
                    $response = array(
                        "cep" => $content['cep'],
                        "logradouro" => $content['logradouro'],
                        "complemento" => $content['complemento'],
                        "bairro" => $content['bairro'],
                        "localidade" => $content['localidade'],
                        "uf" => $content['uf'],
                        "ibge" => $content['ibge'],
                        "gia" => $content['gia'],
                        "ddd" => $content['ddd'],
                        "siafi" => $content['siafi']
                    );
                    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json;
                    $manager->insertCepInfo($content);
                    exit();
                }
            }

        }
    }

    http_response_code(400);
    $response = array(
        "response" => "CEP Inválido",
        "cep" => $formattedCep
    );
    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
    echo $json;
    exit();
}

?>