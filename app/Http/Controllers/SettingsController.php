<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'display_name' => ['nullable', 'string', 'max:255'],
        ]);
        
        $user = $request->user();
        $user->display_name = $request->display_name;
        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Settings saved successfully');
    }
}
