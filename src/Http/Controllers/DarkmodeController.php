<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jeremykenedy\LaravelDarkmodeToggle\Enums\Mode;
use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;

class DarkmodeController extends Controller
{
    /**
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request)
    {
        $field = DarkMode::persistField();

        $request->validate([
            $field => ['required', 'in:'.implode(',', Mode::values())],
        ]);

        $mode = $request->input($field);

        DarkMode::persistFor($request->user(), $mode, $field);

        if ($request->wantsJson()) {
            return response()->json([$field => $mode]);
        }

        return back();
    }
}
