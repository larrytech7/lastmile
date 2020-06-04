<?php

use PHPUnit\Framework\TestCase;
require_once (__DIR__."/../application/utils/AbstractProviderRequest.php");
require_once (__DIR__."/../application/utils/EcobankProvider.php");
require_once (__DIR__."/../application/utils/MobilemoneyProvider.php");
require_once (__DIR__."/../application/utils/OrangeProvider.php");

final class GatewayTest extends TestCase
{
    /** @var Gateway */
    protected $gateway;

    /** @var array */
    private $options;

    public function setUp(){
        parent::setUp();
        $paymentProviders = [
            'MTNMOMO' => MobilemoneyProvider::class,
            'ORANGEMO' => OrangemoneyProvider::class,
            'ECOBANK' => EcobankProvider::class,
            'YUP' => '',
            'EU' => '',
        ];

        $this->options = [
            'providerCallbackHost' =>'http://localhost/ominipay-momo',
            'transaction_amount' => 100.00,
            'transaction_id' => '1234',
            'callback_url' => site_url('payments/gateway/callback'),
            'userId' => 'iamaunifieddev103',
            'password' => '$2a$10$Wmame.Lh1FJDCB4JJIxtx.3SZT0dP2XlQWgj9Q5UAGcDLpB0yRYCC',
        ];

        $this->gateway = new $paymentProviders['ECOBANK']($this->options);

        $this->assertFalse($this->gateway->isRedirect());
        $this->assertSame('http://localhost/ominipay-momo', $this->gateway->getDefaultParameters()['providerCallbackHost']);
        $this->assertInstanceOf(EcobankProvider::class, $this->gateway);
    }

    public function testUpdateEneopay(){
        $gw = new Gateway();
        $data = $gw->updateEneopay([]);
        $this->assertEquals(200, $data['status']);
    }

    public function testPurchase(){
        $data = []; //complete data for given provider
        $this->gateway->purchase($data);
        $this->assertTrue($this->gateway->isRedirect());
    }

    public function testAuthorize(){
        $response = $this->gateway->authorize($this->options);
        //var_dump($response->getMessage());
        $this->assertFalse($response->isRedirect());
    }

}
