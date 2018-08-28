<?php declare(strict_types=1);

namespace Comgate;

use Comgate\Enum\Method;
use Comgate\Request\CreatePayment;
use Comgate\Request\RequestInterface;
use Comgate\Response\CreatePaymentResponse;
use Nette\SmartObject;


/**
 * Class Comgate
 *
 * @author  geniv
 * @package Comgate
 */
class Comgate
{
    use SmartObject;

    /** @var Client */
    private $client;


    /**
     * Comgate constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->client = new Client($parameters['merchantId'], $parameters['sandbox'], $parameters['secret']);
    }


    /**
     * @param int    $price
     * @param string $refId
     * @param string $email
     * @param string $label
     * @param string $method
     * @param string $curr
     * @return CreatePayment
     * @throws Exception\LabelTooLongException
     */
    public function createPayment(int $price, string $refId, string $email, string $label, string $method = Method::ALL, string $curr = 'CZK'): CreatePayment
    {
        $payment = new CreatePayment($price, $refId, $email, $label, $method, $curr);
        return $payment;
    }


    /**
     * @param RequestInterface $request
     * @return CreatePaymentResponse
     */
    public function sendResponse(RequestInterface $request): CreatePaymentResponse
    {
        $createPaymentResponse = $this->client->send($request);
        return $createPaymentResponse;
    }
}
