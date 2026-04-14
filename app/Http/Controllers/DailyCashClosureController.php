<?php

namespace App\Http\Controllers;

use App\Models\DailyCashClosure;
use App\Services\DailyCashDaySummaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyCashClosureController extends Controller
{
    public function __construct(
        private readonly DailyCashDaySummaryService $daySummary,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('cash.close_basic');

        $closures = DailyCashClosure::query()
            ->with('closedBy')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('business_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('business_date', '<=', $request->to))
            ->orderByDesc('business_date')
            ->paginate(15)
            ->withQueryString();

        return view('daily-cash-closures.index', compact('closures'));
    }

    public function create(Request $request): View
    {
        $this->authorize('cash.close_basic');

        $businessDate = old('business_date', $request->query('date', now()->toDateString()));
        $summary = $this->daySummary->summarize($businessDate);

        return view('daily-cash-closures.create', [
            'businessDate' => $summary['business_date'],
            'summary' => $summary,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('cash.close_basic');

        $validated = $request->validate([
            'business_date' => 'required|date',
            'counted_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $existing = DailyCashClosure::where('business_date', $validated['business_date'])->first();
        if ($existing) {
            return back()->withErrors(['business_date' => 'Ya existe un cierre para esa fecha.']);
        }

        $summary = $this->daySummary->summarize($validated['business_date']);
        $expected = (float) $summary['cash_balance_expected'];
        $expectedQr = (float) $summary['qr_balance_expected'];
        $counted = (float) $validated['counted_cash'];
        $difference = $counted - $expected;

        DailyCashClosure::create([
            'business_date' => $validated['business_date'],
            'closed_by_user_id' => auth()->id(),
            'expected_cash' => (string) $expected,
            'counted_cash' => (string) $counted,
            'difference_cash' => (string) $difference,
            'total_qr_day' => (string) $expectedQr,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('daily-cash-closures.index')->with('success', 'Cierre registrado.');
    }

    public function show(DailyCashClosure $dailyCashClosure): View
    {
        $this->authorize('cash.close_basic');

        $dailyCashClosure->load(['closedBy', 'adminReviewedBy']);

        $summary = $this->daySummary->summarize($dailyCashClosure->business_date->toDateString());

        return view('daily-cash-closures.show', [
            'closure' => $dailyCashClosure,
            'summary' => $summary,
        ]);
    }
}
