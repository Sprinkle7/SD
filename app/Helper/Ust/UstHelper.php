<?php


namespace App\Helper\Ust;


// class UstHelper
// {
//     private static function connect()
//     {
//         $client = new \SoapClient("https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
//         return $client;
//     }

//     public static function checkUst($countryCode, $ustId)
//     {
//         $params = array('countryCode' => $countryCode, 'vatNumber' => $ustId);
//         $client = self::connect();
//         dd( $client );
        
//         $r = $client->checkVat($params);
//         if ($r->valid == true) {
//             return true;
//         } else {
//             return false;
//         }
//     }
// }

// public static function checkUst($countryCode, $ustId)
// {
//     $url = "https://ec.europa.eu/taxation_customs/vies/rest-api/ms/$countryCode/vat/$ustId";

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 10);

//     $response = curl_exec($ch);
//     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//     curl_close($ch);

//     if ($httpCode == 200) {
//         $result = json_decode($response, true);
//         return $result['isValid'];
//     } else {
//         // Log the HTTP error code
//         error_log("HTTP Error Code: " . $httpCode);
//         return false;
//     }
// }

class UstHelper
{
    public static function checkUst($countryCode, $ustId)
    {
        $url = "https://evatr.bff-online.de/evatrRPC?UstId_1=DE272188711&UstId_2=$ustId";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200) {
            $xml = simplexml_load_string($response);
            if ($xml === false) {
                error_log("Failed to parse XML");
                return false;
            }
    
            $errorCode = null;
            foreach ($xml->param as $param) {
                foreach ($param->value->array->data as $data) {
                    if ((string)$data->value[0]->string[0] == 'ErrorCode') {
                        $errorCode = (string)$data->value[1]->string[0];
                        break 2; 
                    }
                }
            }

            if ($errorCode == 200) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
