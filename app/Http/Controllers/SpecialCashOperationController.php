<?php

namespace App\Http\Controllers;

use App\Services\SpecialCashService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpecialCashOperationController extends Controller
{
    public function __construct(
        private readonly SpecialCashService $specialCash
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('cash.close_basic');

        $operations = \App\Models\SpecialCashOperation::query()
            ->with(['performedBy', 'sale'])
            ->when($request->filled('type'), fn ($q) => $q->where('operation_type', $request->type))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('special-cash-operations.index', compact('operations'));
    }

    public function create(Request $request): View
    {
        $this->authorize('cash.close_basic');

        $type = $request->get('type', 'bill_break');
        if ($type !== 'bill_break') {
            return redirect()->route('special-cash-operations.create', ['type' => 'bill_break']);
        }

        return view('special-cash-operations.create', ['type' => 'bill_break']);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('cash.close_basic');

        $validated = $request->validate([
            'operation_type' => 'in:bill_break',
            'cash_in' => 'required|numeric|min:0',
            'cash_out' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'breakdown' => 'nullable|string',
        ]);

        $breakdown = [];
        if (! empty($validated['breakdown'] ?? '')) {
            foreach (explode(',', $validated['breakdown']) as $part) {
                $part = trim($part);
                if (preg_match('/^(\d+)\s*x\s*(\d+)$/i', $part, $m)) {
                    $breakdown[$m[2]] = (int) $m[1];
                }
            }
        }

        $this->specialCash->recordBillBreak(
            performedBy: auth()->user(),
            cashIn: (string) $validated['cash_in'],
            cashOutTotal: (string) $validated['cash_out'],
            breakdown: $breakdown,
            description: $validated['description'] ?? null
        );

        return redirect()->route('special-cash-operations.index')->with('success', 'Operación registrada.');
    }
}
