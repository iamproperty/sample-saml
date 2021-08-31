<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use LightSaml\Build\Container\ServiceContainerInterface;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\SamlMessage;

abstract class SAMLController extends Controller
{
    /** @var ServiceContainerInterface */
    protected $samlContainer;

    public function __construct(ServiceContainerInterface $samlContainer)
    {
        $this->samlContainer = $samlContainer;
    }

    /**
     * Return a SAML message to the browser
     *
     * This is only needed for debugging.
     */
    protected function serialise(SamlMessage $message): Response
    {
        $serializationContext = new SerializationContext();
        $message->serialize($serializationContext->getDocument(), $serializationContext);

        return response($serializationContext->getDocument()->saveXML(), 200, [
            'Content-Type' => 'text/xml',
        ]);
    }
}
