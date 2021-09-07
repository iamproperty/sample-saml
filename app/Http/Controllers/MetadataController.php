<?php

namespace App\Http\Controllers;

use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\ContactPerson;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\Organization;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\SamlConstants;

class MetadataController extends SAMlController
{
    public function sp()
    {
        $descriptor = (new EntityDescriptor('sp'))
            ->addItem(
                (new SpSsoDescriptor())
                    ->setWantAssertionsSigned(true)
                    ->setProtocolSupportEnumeration(SamlConstants::PROTOCOL_SAML2)
                    ->addKeyDescriptor((new KeyDescriptor())->setCertificate(
                        X509Certificate::fromFile(storage_path('app/saml/credentials/sp.crt'))
                    ))
                    ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
                    ->addAssertionConsumerService(
                        (new AssertionConsumerService())
                            ->setIsDefault(true)
                            ->setLocation(action('ServiceProviderController@consumer'))
                            ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    )
                    ->addOrganization(
                        (new Organization())
                            ->setLang('en')
                            ->setOrganizationName('Service Provider')
                            ->setOrganizationURL(url('/sp'))
                    )
                    ->addContactPerson(
                        (new ContactPerson())
                            ->setContactType(ContactPerson::TYPE_TECHNICAL)
                            ->setGivenName('James')
                            ->setSurName('Fenwick')
                            ->setEmailAddress('mailto:james.fenwick@iamproperty.com')
                    )
            );

        return $this->serialise($descriptor);
    }

    public function idp()
    {
        $descriptor = (new EntityDescriptor('idp'))
            ->addItem(
                (new IdpSsoDescriptor())
                    ->setProtocolSupportEnumeration(SamlConstants::PROTOCOL_SAML2)
                    ->addKeyDescriptor((new KeyDescriptor())->setCertificate(
                        X509Certificate::fromFile(storage_path('app/saml/credentials/idp.crt'))
                    ))
                    ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
                    ->addSingleSignOnService(
                        (new SingleSignOnService())
                            ->setLocation(action('IdentityProviderController@respond'))
                            ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    )
                    ->addSingleSignOnService(
                        (new SingleSignOnService())
                            ->setLocation(action('IdentityProviderController@respond'))
                            ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    )
                    ->addOrganization(
                        (new Organization())
                            ->setLang('en')
                            ->setOrganizationName('Identity Provider')
                            ->setOrganizationURL(url('/idp'))
                    )
                    ->addContactPerson(
                        (new ContactPerson())
                            ->setContactType(ContactPerson::TYPE_TECHNICAL)
                            ->setGivenName('James')
                            ->setSurName('Fenwick')
                            ->setEmailAddress('mailto:james.fenwick@iamproperty.com')
                    )
            );

        return $this->serialise($descriptor);
    }
}
