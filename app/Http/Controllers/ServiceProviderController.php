<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\Response;
use LightSaml\SamlConstants;

class ServiceProviderController extends SAMLController
{
    public function initiate(Request $request)
    {
        $authnRequest = (new AuthnRequest())
            ->setAssertionConsumerServiceURL(action('ServiceProviderController@consumer'))
            ->setProtocolBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
            ->setID(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setDestination(action('IdentityProviderController@respond'))
            ->setIssuer(new Issuer('sp'));

        // Try to sign the request using stored credentials for the service provider
        $this->tryToSignMessage($authnRequest, ...$this->getCredentialByEntityId('sp'));

        // Use the Redirect binding to send the Authn Request to the Identity Provider
        $redirectBinding = $this->getBindingFactory()->create(SamlConstants::BINDING_SAML2_HTTP_REDIRECT);

        return $redirectBinding->send((new MessageContext())->setMessage($authnRequest));
    }

    public function consumer(Request $request)
    {
        // Deserialise the SAML XML message from the redirect or POST binding
        $binding = $this->getBindingFactory()->getBindingByRequest($request);
        $binding->receive($request, $messageContext = new MessageContext());

        /** @var Response $response */
        $response = $messageContext->getMessage();

        // Extract information from the response
        $attributes = null;
        $subject = null;
        $status = $response->getStatus();
        if ($status->isSuccess()) {
            $subject = $response->getFirstAssertion()->getSubject();
            $attributes = $response->getFirstAssertion()->getFirstAttributeStatement();
        }

        return view('consumer', compact('attributes', 'binding', 'status', 'subject'));
    }
}
