<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("AbstractProviderRequest.php");

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleClient;

class OrangemoneyProvider extends AbstractProviderRequest{

    protected $httpAdapter;
    protected $endpoint = [
        'live' => 'https://apiw.orange.cm/omcoreapis/1.0.2/',
        'test' => 'https://apiw.orange.cm/'
    ];
    protected $baseUrl = "";
    protected $responseData;

    public function __construct($config){
        parent::__construct($config);
        $this->httpAdapter = new GuzzleClient(null);
        $this->responseData = [];
        $this->baseUrl = $this->endpoint[$config['mode']];
    }

    /**
     * Authorize to endpoint to get access token
     *
     * @param array $data authorization data
     * @return HttpResponse object
     */
    public function authorize(array $data = []){
       $auth_url = $this->baseUrl."token";

        // this sensitive information, we need to write this in config.php and encrypt it
        $consumer_key = $this->configs['orange-consumer-key'];
        $consumer_secret = $this->configs['orange-consumer-secret'];
        $encodedsecret = base64_encode($consumer_key . ':' . $consumer_secret);
        $headers = array();
        $headers[] = 'Authorization: ' . 'Basic ' . $encodedsecret;
        $headers[] = 'content-type: application/x-www-form-urlencoded';

        $fields = sprintf("grant_type=%s&username=%s&password=%s", 'client_credentials', $this->configs['orange-api-user'],$this->configs['orange-api-password']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        if ($err) {
            return [
                'status' => 400,
                'message' => $err
            ];
        } else {
            $result = json_decode($response, true);
            log_message('error', 'OrangeMo token result '.$result['access_token']);
            return [
                    'status' => 200,
                    'message' => 'ok',
                    'data' => [
                        'token' => $result['access_token'],
                        'data' => $result
                    ]
                ];
        }
    }

    public function purchase(array $data = []){
        $authData = $this->authorize($data); //get token

        if($authData['status'] == 200 ){
            $header = [
                'Authorization' => 'Bearer '. $authData['data']['token'],
                'X-AUTH-TOKEN' => base64_encode($this->configs['orange-api-user'].':'.$this->configs['orange-api-password']),
                'verify' => false
            ];

            //obtain a payToken
            $headers = array();
            $headers[] = 'Authorization: ' . $header['Authorization'];
            $headers[] = 'X-AUTH-TOKEN: ' . $header['X-AUTH-TOKEN'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://apiw.orange.cm/omcoreapis/1.0.2/' . "mp/init");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            
            if ($err) {
                return [
                    'status' => 400,
                    'message' => $err
                ];
            } else {
                $result = json_decode($response, true);
                $payToken = $result['data']['payToken'];
                log_message('error',  'Orange payment payToken : ' . $payToken);
                
                    //Cash out request
                $payload = [
                    'amount' => $data['transaction_amount'] ?? 1,
                    'notifUrl' => $this->configs['callback_url'],
                    'channelUserMsisdn' => '694849648',
                    'subscriberMsisdn' => $data['phone_number'],
                    'pin' => '2222',
                    'orderId' => $data['transaction_id'] ?? random_string(),
                    'description' => 'Online Bill payment',
                    'payToken' => $payToken
                ];
                $headers[] = 'Content-Type : application/json';
                $headers[] = 'Content-Length : '.strlen(json_encode($payload));
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://apiw.orange.cm/'. "omcoreapis/1.0.2/mp/pay");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                $response = curl_exec($ch);
                $err = curl_error($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if($err){
                    $this->responseData =  [
                        'status' => $code,
                        'message' => $data['message']
                    ];
                    return $this->responseData;
                }else{
                    $result = json_decode($response, true);
                    $this->responseData =  [
                        'status' => $code,
                        'message' => $result['message'],
                        'data' => $result['data']
                    ];
                    return $this->responseData;
                }
            }
            
        }else{
            return [
                'error' => 'Error ocurred with the request. Please try again',
                'status' => $authData['status'],
                'message' => $authData['message'],
            ];
        }
    }

    public function refund(array $data = []){
        throw new Exception('Refund not supported for this provider', 400);
    }

    public function isRedirect(){
        return array_key_exists('response_content', $this->responseData);
    }

    public function getRedirectUrl(){
        return array_key_exists('response_content', $this->responseData) ? $this->responseData['response_content'] : "";
    }

    private function generateUuidV4(){
        // v4 generate uuid
        if (function_exists('com_create_guid') === true)
			return trim(com_create_guid(), '{}');
	
			$data = openssl_random_pseudo_bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function sandboxUser($configs){
        $url = "https://ericssonbasicapi1.azure-api.net/provisioning/v1_0/apiuser";
        $header = [
            'X-Reference-Id' => $this->generateUuidV4(),
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $body = [
            "providerCallbackHost" => $configs['callback_url'] ?? '',
        ];
        $req = new Request('POST', $url, $header, json_encode($body));
        $response = $this->httpAdapter->sendRequest($req);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'user' => $response->getStatusCode() == 201 ? $header['X-Reference-Id'] : ''
            ]
        ];
    }
    
    public function sandboxApi($configs, $user){
        $url = "https://ericssonbasicapi1.azure-api.net/provisioning/v1_0/apiuser/".$user."/apikey";
        $header = [
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $req = new Request('POST', $url, $header);
        $response = $this->httpAdapter->sendRequest($req);
        $api = json_decode($response->getBody()->getContents(), true);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'api-key' => $response->getStatusCode() == 201 ? $api['apiKey'] : ''
            ]
        ];
    }

    public function sandboxToken($configs){
        $url = "https://ericssonbasicapi1.azure-api.net/token";
        $header = [
            'Authorization' => 'Basic '. base64_encode($configs['api_user'].':'.$configs['api_key']),
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $req = new Request('POST', $url, $header);
        $response = $this->httpAdapter->sendRequest($req);
        $token = json_decode($response->getBody()->getContents(), true);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'token' => $token['token']
            ]
        ];
    }
    
    public function sandboxPay($configs, $body){
        $url = "https://ericssonbasicapi1.azure-api.net/requesttopay";
        $header = [
            'Authorization' => 'Bearer '. $configs['token'],
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $req = new Request('POST', $url, $header, json_encode($body));
        $response = $this->httpAdapter->sendRequest($req);
        $payment = json_decode($response->getBody()->getContents(), true);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'payment' => $payment
            ]
        ];
    }
}