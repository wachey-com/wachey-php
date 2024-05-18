<?php
namespace Wachey\Api;

class Report {

    public function __construct() {

    }

    public static function error($error = null, $path = null, $line = null, $ip = null, $user = null) {
    
        try {

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, 'https://api.wachey.com/report/error');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 

            $data = array(
                'api_key' => 'QztESaXtTNjsCWCpDcvH3xowQztESaXtTNjsCWCpDcvH3xognchmr8jq3rhqcwroaxqb269c65mlfXNtxnXyiq5uNBbb269c65', 
                'password' => 'QztESaXtTNjsCWCpDcvH3xowQztESaXtTNjsCWCpDcvH3xognchmr8jq3rhqcwroaxqb269c65mlfXNtxnXyiq5uNBbb269c65',
                'error' => $error,
                'path' => $path,
                'line' => $line,
                'ip' => $ip,
                'user' => $user
            );

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($curl);
            curl_close($curl);

            return json_decode($response);

        } 
        
        catch (\Exception $e) {

            return $e->getMessage();

        }

       
    }


}