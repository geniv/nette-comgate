<?php declare(strict_types=1);

namespace Comgate;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\SmartObject;


/**
 * Class FileResponse
 *
 * @author  geniv
 * @package Comgate
 */
class ComgateResponse implements \Nette\Application\IResponse
{
    use SmartObject;

    /** @var string */
    private $data;


    /**
     * ComgateResponse constructor.
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }


    /**
     * Sends response to output.
     *
     * @param IRequest  $httpRequest
     * @param IResponse $httpResponse
     * @return void
     */
    public function send(IRequest $httpRequest, IResponse $httpResponse)
    {
        $httpResponse->setContentType('text/plain', 'utf-8');
        $httpResponse->setCode(200);
        echo $this->data;
    }
}
