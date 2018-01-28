<?php namespace RevoSystems\DBSync;

use GuzzleHttp\Client;

class FileSender {
    
    protected $client;
    protected $endpoint;
    
    public static function make($endpoint) {
        $fileSender = new FileSender;
        $fileSender->client = new Client();
        $fileSender->endpoint = $endpoint;
        return $fileSender;
    }
    
    public function sendFile($pathToFile) {
        $client = new Client();
        $client->request('POST', '/post', [
            'multipart' => [
                [
                    'name'     => 'foo',
                    'contents' => 'data',
                    'headers'  => ['X-Baz' => 'bar']
                ],
                [
                    'name'     => 'baz',
                    'contents' => fopen($pathToFile, 'r')
                ],
                [
                    'name'     => 'qux',
                    'contents' => fopen('/path/to/file', 'r'),
                    'filename' => 'custom_filename.txt'
                ],
            ]
        ]);
    }
    
}