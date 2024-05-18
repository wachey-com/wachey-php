<?php
namespace Wachey\Api;

class Report {

    private $error;
    private $path;
    private $line;
    private $ip;
    private $user;

    public function __construct($error = null, $path = null, $line = null, $ip = null, $user = null) {
        echo "Report class loaded";

        $this->error = $error;
        $this->path = $path;
        $this->line = $line;
        $this->ip = $ip;
        $this->user = $user;

    }

    public function error() {
    
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query([
                    'api_key' => 'QztESaXtTNjsCWCpDcvH3xowQztESaXtTNjsCWCpDcvH3xognchmr8jq3rhqcwroaxqb269c65mlfXNtxnXyiq5uNBbb269c65', 
                    'password' => 'QztESaXtTNjsCWCpDcvH3xowQztESaXtTNjsCWCpDcvH3xowmlfXNtxnXyiq5uNBbb269c65mlfXNtxnXyiq5uNBbb269c65',
                    'error' => $this->error,
                    'path' => $this->path,
                    'line' => $this->line,
                    'ip' => $this->ip,
                    'user' => $this->user,
                ]),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents('https://api.wachey.com/report/error', false, $context);
        
    }


}