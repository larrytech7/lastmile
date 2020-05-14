<?php

use PHPUnit\Framework\TestCase;
require_once (__DIR__."/../application/utils/EcobankProvider.php");

final class GatewayTest extends TestCase
{
    /** @var Gateway */
    protected $gateway;

    /** @var array */
    private $options;

    public function setUp(){
        parent::setUp();
        $paymentProviders = [
            'MTNMOMO' => EcobankProvider::class,
            'ORANGEMO' => '',
            'ECOBANK' => EcobankProvider::class,
            'YUP' => '',
            'EU' => '',
        ];

        $this->gateway = new $paymentProviders['ECOBANK'];

        $this->options = [
            'providerCallbackHost' =>'http://localhost/ominipay-momo',
            'transaction_amount' => 100.00,
            'transaction_id' => '',
            'callback_url' => site_url('payments/gateway/callback'),
            'userId' => 'iamaunifieddev103',
            'password' => '$2a$10$Wmame.Lh1FJDCB4JJIxtx.3SZT0dP2XlQWgj9Q5UAGcDLpB0yRYCC',
        ];

        $this->assertFalse($this->gateway->isRedirect());
        $this->assertSame('http://localhost/ominipay-momo', $this->gateway->getDefaultParameters()['providerCallbackHost']);
        $this->assertInstanceOf(EcobankProvider::class, $this->gateway);
    }

    public function testPurchase(){
        $this->gateway->purchase($this->options);
        $this->assertTrue($this->gateway->isRedirect());
    }

    public function testAuthorize(){
        $response = $this->gateway->authorize($this->options);
        //var_dump($response->getMessage());
        $this->assertFalse($response->isRedirect());
    }

}
