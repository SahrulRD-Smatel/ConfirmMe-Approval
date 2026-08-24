<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        if (in_array($locale, ['en', 'id'])) {
            if (Auth::check()) {
                $user = Auth::user();
                $user->locale = $locale;
                $user->save();
            }
            $request->session()->put('locale', $locale);
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
