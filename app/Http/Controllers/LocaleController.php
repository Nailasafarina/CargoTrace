<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch application locale for current session and user profile.
     */
    public function setLocale(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, ['id', 'en'], true)) {
            abort(400, 'Invalid locale. Supported locales: id, en.');
        }

        $request->session()->put('locale', $locale);
        $request->session()->save();

        if ($request->user()) {
            $request->user()->update(['locale' => $locale]);
        }

        $previousUrl = url()->previous();
        if (empty($previousUrl) || str_contains($previousUrl, '/locale/')) {
            $previousUrl = route('home');
        }

        return redirect()->to($previousUrl)->withCookie(cookie()->forever('locale', $locale));
    }
}
