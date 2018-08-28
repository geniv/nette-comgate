<?php declare(strict_types=1);

namespace Comgate\Bridges\Nette;

use Comgate\Comgate;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Comgate\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'merchantId' => null,
        'secret'     => null,
        'sandbox'    => false,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $builder->addDefinition($this->prefix('default'))
            ->setFactory(Comgate::class, [$config])
            ->setAutowired(true);
    }
}
