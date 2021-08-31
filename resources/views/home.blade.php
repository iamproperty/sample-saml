<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">

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
    </div>
</body>
</html>
