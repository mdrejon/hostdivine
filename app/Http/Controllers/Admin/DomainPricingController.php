<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainTld;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DomainPricingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/DomainPricing/Index', [
            'tlds' => DomainTld::query()->orderBy('sort_order')->orderBy('tld')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        DomainTld::query()->create($this->validated($request));

        return back()->with('success', 'Domain extension added.');
    }

    public function update(Request $request, DomainTld $tld): RedirectResponse
    {
        $tld->update($this->validated($request, $tld));

        return back()->with('success', 'Domain extension updated.');
    }

    /**
     * Add the extension to the end of the "popular" list, or remove it.
     */
    public function togglePopular(DomainTld $tld): RedirectResponse
    {
        $tld->update(['popular_order' => $tld->popular_order ? null : (int) DomainTld::query()->max('popular_order') + 1]);

        return back()->with('success', $tld->popular_order ? 'Added to popular extensions.' : 'Removed from popular extensions.');
    }

    public function destroy(DomainTld $tld): RedirectResponse
    {
        $tld->delete();

        return back()->with('success', 'Domain extension deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, ?DomainTld $tld = null): array
    {
        $validated = $request->validate([
            'tld' => ['required', 'string', 'max:60', 'regex:/^\.[a-z0-9.-]+$/', Rule::unique('domain_tlds', 'tld')->ignore($tld?->id)],
            'price_1yr' => ['nullable', 'numeric', 'min:0'],
            'price_2yr' => ['nullable', 'numeric', 'min:0'],
            'price_3yr' => ['nullable', 'numeric', 'min:0'],
            'price_5yr' => ['nullable', 'numeric', 'min:0'],
            'price_10yr' => ['nullable', 'numeric', 'min:0'],
            'transfer_price' => ['nullable', 'numeric', 'min:0'],
            'renew_prices' => ['nullable', 'array'],
            'renew_prices.*' => ['nullable', 'numeric', 'min:0'],
            'badge' => ['nullable', 'string', 'max:30'],
            'popular_order' => ['nullable', 'integer', 'min:1'],
            'featured_order' => ['nullable', 'integer', 'min:1'],
            'renew_order' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ], ['tld.regex' => 'Start with a dot, e.g. .com or .com.bd']);

        $renewals = array_filter($validated['renew_prices'] ?? [], fn ($price) => $price !== null && $price !== '');
        $validated['renew_prices'] = $renewals ?: null;
        $validated['sort_order'] ??= 0;

        return $validated;
    }
}
