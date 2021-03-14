<?php

namespace App\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tzsk\Sms\Contracts\Driver;

class Farazsms extends Driver
{

    protected array $settings;

    protected Client $client;

    protected string $url;

    protected string $mode = 'default';

    protected array $pattern = [];

    public function __construct(array $settings) {
        $this->settings = $settings;
        $this->client = new Client();
    }

    public function send() {
        if ($this->body === 'pattern') {
            $this->setMode('pattern');
        }
        $response = collect();

        foreach ($this->recipients as $recipient) {
            if ($this->mode === 'default') {
                $result = $this->sendDefault($recipient);
            }
            if ($this->mode === 'pattern') {
                $result = $this->sendPattern($recipient);
            }
            $response->put($recipient , json_decode((string)$result->getBody()));
        }

        return (count($this->recipients) == 1) ? $response->first() : $response;
    }

    public function setMode(string $mode , array $patternParams = NULL) {
        $this->mode = $this->settings['modes'][$mode] ? $mode : 'default';
        if ($mode === 'pattern' && $patternParams && $patternParams['code'] && $patternParams['inputs']) {
            $this->pattern($patternParams['code'] , $patternParams['inputs']);
        }

        return $this;
    }

    public function pattern(string $pattern , array $inputs , string|array $to = NULL)
    {
        if ($to) $this->to($to);

        $this->pattern['code'] = $pattern;
        $this->pattern['inputs'] = $inputs;

        return $this;
    }

    protected function sendDefault($recipient){
        return $this->client->request(
            'POST' ,
            data_get($this->settings['modes']['default'] , 'url') ,
            $this->payload($recipient)
        );
    }

    protected function payload($recipient): array {
        return [
            'form_params' => [
                'uname'   => data_get($this->settings , 'username') ,
                'pass'    => data_get($this->settings , 'password') ,
                'from'    => data_get($this->settings , 'from') ,
                'message' => $this->body ,
                'to'      => json_encode([$recipient]) ,
                'op'      => 'send' ,
            ] ,
        ];
    }

    protected function sendPattern($recipient) {
        $payload = [
            'username'     => $this->settings['username'] ,
            'password'     => $this->settings['password'] ,
            'from'         => $this->settings['from'] ,
            'to'           => json_encode($recipient) ,
            'pattern_code' => $this->pattern['code'] ,
            'input_data'   => json_encode($this->pattern['inputs']) ,
        ];

        $result = $this->client->get(data_get($this->settings['modes']['pattern'] , 'url') , ['query' => $payload]);

        return $result;
    }
}
