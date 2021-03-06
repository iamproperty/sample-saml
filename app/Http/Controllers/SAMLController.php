<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use LightSaml\Binding\BindingFactory;
use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\X509CredentialInterface;
use LightSaml\Error\LightSamlSecurityException;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;

abstract class SAMLController extends Controller
{
    /** @var BuildContainerInterface */
    protected $samlContainer;

    public function __construct(BuildContainerInterface $samlContainer)
    {
        $this->samlContainer = $samlContainer;
    }

    protected function getBindingFactory(): BindingFactory
    {
        return $this->samlContainer->getServiceContainer()->getBindingFactory();
    }

    /**
     * @return CredentialInterface[]
     */
    protected function getCredentialByEntityId(string $entityId): array
    {
        Log::debug("Looking for credentials for entity ID: $entityId");
        $credentials = $this->samlContainer
            ->getCredentialContainer()
            ->getCredentialStore()
            ->getByEntityId($entityId);
        Log::debug(sprintf('Found %d credential(s) for entity ID: %s', count($credentials), $entityId));

        return $credentials;
    }

    protected function tryToSignMessage(SamlMessage $message, CredentialInterface ...$credentials): void
    {
        foreach ($credentials as $credential) {
            // If any credential is an X509 credential sign the request
            if ($credential instanceof X509CredentialInterface) {
                Log::info('Signing message with credential: '.json_encode($credential->getKeyNames()));
                $message->setSignature(new SignatureWriter($credential->getCertificate(), $credential->getPrivateKey()));
                return;
            }
            Log::debug('Credential not suitable for signing');
        }
    }

    protected function sendMessage(
        SamlMessage $message,
        string $bindingType = SamlConstants::BINDING_SAML2_HTTP_POST
    ): \Symfony\Component\HttpFoundation\Response {
        // Get the binding type requested in the Authn Request
        $binding = $this->getBindingFactory()->create($bindingType);

        return $binding->send((new MessageContext())->setMessage($message));
    }

    /**
     * Return a SAML message to the browser
     *
     * This is only needed for debugging.
     */
    protected function serialise(AbstractSamlModel $message): Response
    {
        $serializationContext = new SerializationContext();
        $message->serialize($serializationContext->getDocument(), $serializationContext);

        return response($serializationContext->getDocument()->saveXML(), 200, [
            'Content-Type' => 'text/xml',
        ]);
    }

    protected function signatureMatches(SamlMessage $message): bool
    {
        $signature = $message->getSignature();

        // Get credentials for the issuer, and try each against the signature
        foreach ($this->getCredentialByEntityId($message->getIssuer()->getValue()) as $credential) {
            try {
                if ($signature->validate($credential->getPublicKey())) {
                    // The signature has been validated
                    Log::debug('Message signature matches credential: '.json_encode($credential->getKeyNames()));
                    return true;
                }
            } catch (LightSamlSecurityException $e) {
                // The credential didn't match the signature, but another might
                Log::debug("Message signature didn't match credential: ".json_encode($credential->getKeyNames()));
            }
        }

        return false;
    }
}
