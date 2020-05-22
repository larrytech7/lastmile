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

                //initiate user payment prompt
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://apiw.orange.cm/'. "omcoreapis/1.0.2/mp/push/".$payToken);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
                        'status' => $result['data']['status'],
                        'message' => $result['message'] . '. Enter your PIN on your mobile to confirm.',
                        'error' => $result['data']['status'] == 'PENDING' ? '' : $result['message'],
                        'data' => [
                            'message' => $result['message'], 
                            'init' =>$result['data']['inittxnmessage'], 
                            'transaction_id' => base64_encode($data['transaction_id']),
                            'paytoken' => base64_encode($payToken),
                            'auth-token' => base64_encode($header['Authorization']),
                            'x-token' => base64_encode($header['X-AUTH-TOKEN']),
                            'redirect_url' =>$this->configs['callback_url'],
                        ]
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

}