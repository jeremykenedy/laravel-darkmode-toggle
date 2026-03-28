<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DarkmodeController extends Controller
{
    public function update(Request $request)
    {
        $field = config('darkmode.persist_field', 'dark_mode');

        $request->validate([
            $field => ['required', 'in:light,dark,system'],
        ]);

        $user = $request->user();

        if ($user && method_exists($user, 'ensureProfile')) {
            $user->ensureProfile();
            $user->profile->update([$field => $request->input($field)]);
        }

        if ($request->wantsJson()) {
            return response()->json([$field => $request->input($field)]);
        }

        return back();
    }
}
