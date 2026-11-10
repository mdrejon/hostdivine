<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    public function clearCache(): RedirectResponse
    {
        Setting::clearCache();

        Artisan::call('optimize:clear');

        return back()->with('success', 'Application cache cleared & optimized successfully.');
    }
}
