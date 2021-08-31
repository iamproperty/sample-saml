<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'id' => 'required',
            'email' => 'sometimes|email',
        ]);

        $request->session()->put('user', (object)$validated);

        return redirect('/');
    }

    public function destroy(Request $request)
    {
        $request->session()->remove('user');

        return redirect('/');
    }
}
