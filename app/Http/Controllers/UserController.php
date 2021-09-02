<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'id' => 'required',
            'email' => 'sometimes|email',
            'given_name' => 'sometimes',
            'surname' => 'sometimes',
        ]);
        Log::info('Adding user information to session');

        $request->session()->put('user', (object)$validated);

        return redirect('/');
    }

    public function destroy(Request $request)
    {
        Log::info('Removing user information from session');
        $request->session()->remove('user');

        return redirect('/');
    }
}
