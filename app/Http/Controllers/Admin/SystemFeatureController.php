<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SystemFeatureController extends Controller
{
    public function index()
    {
        $features = DB::table('system_features')->orderBy('id')->get();
        return view('admin.features.index', compact('features'));
    }

    public function toggle(Request $request, $key)
    {
        $feature = DB::table('system_features')->where('key', $key)->first();

        if (!$feature) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Fitur tidak ditemukan.'], 404);
            }
            return back()->with('error', 'Fitur tidak ditemukan.');
        }

        $newState = !$feature->is_active;

        DB::table('system_features')->where('key', $key)->update([
            'is_active' => $newState,
            'updated_at' => now(),
        ]);

        // Clear cache
        Cache::forget("system_feature_{$key}");

        $statusName = $newState ? 'diaktifkan' : 'dinonaktifkan';
        $message = "Fitur {$feature->name} berhasil {$statusName}.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $newState,
                'name' => $feature->name,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
