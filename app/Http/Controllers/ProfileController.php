<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\TotpService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $qrSvg = null;
        $secretKey = null;

        if (!$user->hasTwoFactorEnabled() && session()->has('two_factor_setup_secret')) {
            $secretKey = session()->get('two_factor_setup_secret');
            $company = config('app.name', 'ConfirmMe');
            $qrUri = TotpService::getQrUri($company, $user->email, $secretKey);
            $qrSvg = QrCode::format('svg')->size(160)->margin(1)->generate($qrUri);
        }

        return view('profile.edit', [
            'user' => $user,
            'qrSvg' => $qrSvg,
            'secretKey' => $secretKey,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->to(route('profile.edit') . '#profile-info')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,gif', 'max:2048'],
        ], [
            'avatar.required' => 'Silakan pilih foto.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus JPG, PNG, atau GIF.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->avatar = $path;
        $user->save();

        return redirect()->to(route('profile.edit') . '#profile-photo')->with('status', 'photo-updated');
    }

    /**
     * Delete the user's profile photo.
     */
    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return redirect()->to(route('profile.edit') . '#profile-photo')->with('status', 'photo-deleted');
    }

    /**
     * Update the user's bank account information.
     */
    public function updateBank(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
        ]);

        $request->user()->fill($validated);
        $request->user()->save();

        return redirect()->to(route('profile.edit') . '#bank-info')->with('status', 'bank-updated');
    }

    /**
     * Start Two-Factor Authenticator setup by generating a secret.
     */
    public function enableTwoFactor(Request $request): RedirectResponse
    {
        $secret = TotpService::generateSecret();
        session()->put('two_factor_setup_secret', $secret);

        return redirect()->to(route('profile.edit') . '#two-factor-auth')->with('status', 'two-factor-setup-started');
    }

    /**
     * Cancel Two-Factor Authenticator setup.
     */
    public function cancelTwoFactorSetup(Request $request): RedirectResponse
    {
        session()->forget('two_factor_setup_secret');

        return redirect()->to(route('profile.edit') . '#two-factor-auth');
    }

    /**
     * Confirm and activate Two-Factor Authenticator with the first valid 6-digit code.
     */
    public function confirmTwoFactor(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorConfirm', [
            'code' => ['required', 'string'],
        ]);

        $secret = session()->get('two_factor_setup_secret');
        if (!$secret) {
            return redirect()->to(route('profile.edit') . '#two-factor-auth')
                ->withErrors(['code' => __('Sesi setup autentikator telah kedaluwarsa. Silakan mulai kembali.')], 'twoFactorConfirm');
        }

        if (!TotpService::verifyCode($secret, $request->code)) {
            return redirect()->to(route('profile.edit') . '#two-factor-auth')
                ->withErrors(['code' => __('Kode autentikator 6 digit tidak cocok. Pastikan jam di ponsel Anda sudah sinkron otomatis.')], 'twoFactorConfirm');
        }

        $recoveryCodes = TotpService::generateRecoveryCodes();
        $user = $request->user();
        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        session()->forget('two_factor_setup_secret');
        session()->flash('recovery_codes', $recoveryCodes);

        return redirect()->to(route('profile.edit') . '#two-factor-auth')->with('status', 'two-factor-enabled');
    }

    /**
     * Disable Two-Factor Authenticator.
     */
    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorDisable', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        session()->forget('two_factor_setup_secret');

        return redirect()->to(route('profile.edit') . '#two-factor-auth')->with('status', 'two-factor-disabled');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
