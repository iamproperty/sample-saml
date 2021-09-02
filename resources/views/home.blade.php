@extends('layout')
@section('content')
    <div class="row mt-3">
        <div class="col-lg-6">
            <p>This will begin the Service Provider (<dfn><abbr>SP</abbr></dfn>) initiated flow. <br>
                This sends an <code>AuthnRequest</code> to the Identity Provider (<dfn><abbr>IdP</abbr></dfn>),
                which response with a <code>Response</code> to the Assertion Consumer Service (<dfn><abbr>ACS</abbr></dfn>) URL.
            </p>
            <a href="{{ action('ServiceProviderController@initiate') }}" class="btn btn-link">Login</a>

            <p>This will begin the IdP initiated flow. <br>
                This sends a <code>Response</code> to the SP ACS URL directly.
            </p>
            <a href="{{ action('IdentityProviderController@initiate') }}" class="btn btn-link">Login</a>
        </div>

        <div class="col-lg-6 border py-2">
            <div class="row">
                <div class="mb-3 form-text">Set the current user for the Identity Provider (IdP) here</div>

                <div class="col-md-6">
                    <form action="{{ url('/user') }}" method="post">
                        @csrf

                        <div class="mb-3">
                            <label for="id">Id</label>
                            <input type="text" id="id" name="id" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="given_name">Given name</label>
                            <input type="text" id="given_name" name="given_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="surname">Surname</label>
                            <input type="text" id="surname" name="surname" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>

                <div class="col-md-6 d-flex flex-column justify-content-between">
<pre><code>{{ json_encode(session('user', new stdClass), JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR) }}</code></pre>

                    <form action="{{ url('/user') }}" method="post">
                        @csrf @method('delete')
                        <button type="submit" class="btn btn-danger">Clear</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
