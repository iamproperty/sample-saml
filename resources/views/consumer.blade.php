@extends('layout')
@section('content')
    <div class="row mt-3">
        <p>These values are extracted from the SAML Response sent from the IdP to the SP.</p>
        @if($binding instanceof \LightSaml\Binding\HttpRedirectBinding)
            <p>This used the HTTP Redirect Binding</p>
        @elseif ($binding instanceof \LightSaml\Binding\HttpPostBinding)
            <p>This used the HTTP POST Binding</p>
        @endif
        <dl>
        @if($status->isSuccess())
            <dt>Subject</dt>
            <dd>{{ $subject->getNameID()->getValue() }}</dd>
            <dt>Email</dt>
            <dd>{{ optional($attributes->getFirstAttributeByName(\LightSaml\ClaimTypes::EMAIL_ADDRESS))->getFirstAttributeValue() }}</dd>
            <dt>Given name</dt>
            <dd>{{ optional($attributes->getFirstAttributeByName(\LightSaml\ClaimTypes::GIVEN_NAME))->getFirstAttributeValue() }}</dd>
            <dt>Surname</dt>
            <dd>{{ optional($attributes->getFirstAttributeByName(\LightSaml\ClaimTypes::SURNAME))->getFirstAttributeValue() }}</dd>
        @else
            <dt>Status code</dt>
            <dd>{{ $status->getStatusCode()->getValue() }}</dd>
            <dt>Status message</dt>
            <dd>{{ $status->getStatusMessage() }}</dd>
        @endif
        </dl>
    </div>
@endsection
