<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use LightSaml\ClaimTypes;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\SamlConstants;

class IdentityProviderController extends SAMlController
{
    public function respond(Request $request)
    {
        // Get the fake user from the session. This would normally use the Laravel auth guard.
        $user = new GenericUser((array)$request->session()->get('user'));

        // Deserialise the SAML XML message from the redirect or POST binding
        $binding = $this->getBindingFactory()->getBindingByRequest($request);
        $binding->receive($request, $messageContext = new MessageContext());

        /** @var AuthnRequest $authnRequest */
        $authnRequest = $messageContext->getMessage();

        $response = (new Response())
            ->setID(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setDestination($authnRequest->getAssertionConsumerServiceURL())
            ->setIssuer(new Issuer('idp'));

        // If the request is signed try to verify the signature
        if ($authnRequest->getSignature() && !$this->signatureMatches($authnRequest)) {
            // No credential matching the signature was found, so respond to the SP with an error
            $response->setStatus(
                new Status(
                    new StatusCode(SamlConstants::STATUS_REQUESTER),
                    'Unable to verify signature'
                )
            );

            // Send the response with the binding type requested in the Authn Request
            return $this->sendMessage($response, $authnRequest->getProtocolBinding());
        }

        $response->addAssertion($assertion = new Assertion())
            ->setStatus((new Status)->setSuccess());

        $assertion
            ->setId(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setIssuer($response->getIssuer())
            ->setSubject(
                (new Subject())
                    ->setNameID(
                        new NameID(
                            $user->getAuthIdentifier(),
                            'http://schemas.microsoft.com/identity/claims/objectidentifier'
                        )
                    )
            )
            ->setConditions(
                // Conditions place limits on how the Service Provider should accept the response
                (new Conditions())
                    ->setNotBefore(new DateTime())
                    ->setNotOnOrAfter(new DateTime('+10 MINUTE'))
            )
            ->addItem(
                // Attribute Statements provide information about the subject
                (new AttributeStatement())
                    ->addAttribute(new Attribute(ClaimTypes::EMAIL_ADDRESS, e($user->email)))
                    ->addAttribute(new Attribute(ClaimTypes::GIVEN_NAME, e($user->given_name)))
                    ->addAttribute(new Attribute(ClaimTypes::SURNAME, e($user->surname)))
            )
            ->addItem(
                // Authn Statements give the Service Provider information about how the subject was authenticated
                // e.g. some service providers might require the subject has use MFA
                (new AuthnStatement())
                    ->setAuthnInstant(new DateTime('-10 MINUTE'))
                    ->setAuthnContext(
                        (new AuthnContext())
                            ->setAuthnContextClassRef('urn:oasis:names:tc:SAML:2.0:ac:classes:PreviousSession')
                    )
            );

        // Send the response with the binding type requested in the Authn Request
        return $this->sendMessage($response, $authnRequest->getProtocolBinding());
    }

    public function initiate(Request $request)
    {
        // Get the fake user from the session. This would normally use the Laravel auth guard.
        $user = new GenericUser((array)$request->session()->get('user'));

        $response = (new Response())
            ->addAssertion($assertion = new Assertion())
            ->setStatus(new Status(
                new StatusCode(SamlConstants::STATUS_SUCCESS)
            ))
            ->setID(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setDestination(action('ServiceProviderController@consumer'))
            ->setIssuer(new Issuer('idp'));

        $assertion
            ->setId(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setIssuer($response->getIssuer())
            ->setSubject(
                (new Subject())
                    ->setNameID(
                        new NameID(
                            $user->getAuthIdentifier(),
                            'http://schemas.microsoft.com/identity/claims/objectidentifier'
                        )
                    )
            )
            ->setConditions(
                // Conditions place limits on how the Service Provider should accept the response
                (new Conditions())
                    ->setNotBefore(new DateTime())
                    ->setNotOnOrAfter(new DateTime('+10 MINUTE'))
            )
            ->addItem(
                // Attribute Statements provide information about the subject
                (new AttributeStatement())
                    ->addAttribute(new Attribute(ClaimTypes::EMAIL_ADDRESS, e($user->email)))
                    ->addAttribute(new Attribute(ClaimTypes::GIVEN_NAME, e($user->given_name)))
                    ->addAttribute(new Attribute(ClaimTypes::SURNAME, e($user->surname)))
            )
            ->addItem(
                // Authn Statements give the Service Provider information about how the subject was authenticated
                // e.g. some service providers might require the subject has use MFA
                (new AuthnStatement())
                    ->setAuthnInstant(new DateTime('-10 MINUTE'))
                    ->setAuthnContext(
                        (new AuthnContext())
                            ->setAuthnContextClassRef('urn:oasis:names:tc:SAML:2.0:ac:classes:PreviousSession')
                    )
            );

        // Try to sign the response using stored credentials for the identity provider
        $this->tryToSignMessage($response, ...$this->getCredentialByEntityId('idp'));

        // Send the response using the HTTP Redirect Binding
        return $this->sendMessage($response, SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
    }
}
