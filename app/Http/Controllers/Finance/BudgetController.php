<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of budgets.
     */
    public function index(Request $request)
    {
        $budgets = Budget::with('creator')
            ->when($request->filled('year'), function ($q) use ($request) {
                $q->where('fiscal_year', $request->year);
            })
            ->when($request->filled('department'), function ($q) use ($request) {
                $q->where('department', $request->department);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('is_active', $request->status === 'active');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalBudget = Budget::sum('total_amount');
        $usedBudget = Budget::sum('used_amount');
        $availableBudget = $totalBudget - $usedBudget;
        $activeBudgets = Budget::where('is_active', true)->count();

        return view('finance.budgets.index', compact('budgets', 'totalBudget', 'usedBudget', 'availableBudget', 'activeBudgets'));
    }

    /**
     * Show the form for creating a new budget.
     */
    public function create()
    {
        return view('finance.budgets.form');
    }

    /**
     * Store a newly created budget.
     */
    public function store(Request $request)
    {
        // Sanitize currency inputs (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['total_amount']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:budgets,code',
            'description' => 'nullable|string',
            'department' => 'nullable|string|max:100',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'total_amount' => 'required|numeric|min:0',
        ]);

        Budget::create([
            ...$validated,
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        return redirect()->route('finance.budgets.index')
            ->with('success', 'Anggaran berhasil dibuat!');
    }

    /**
     * Display the specified budget.
     */
    public function show(Budget $budget)
    {
        return view('finance.budgets.show', compact('budget'));
    }

    /**
     * Show the form for editing the budget.
     */
    public function edit(Budget $budget)
    {
        return view('finance.budgets.form', compact('budget'));
    }

    /**
     * Update the specified budget.
     */
    public function update(Request $request, Budget $budget)
    {
        // Sanitize currency inputs (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['total_amount']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:budgets,code,' . $budget->id,
            'description' => 'nullable|string',
            'department' => 'nullable|string|max:100',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'total_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $budget->update($validated);

        return redirect()->route('finance.budgets.index')
            ->with('success', 'Anggaran berhasil diperbarui!');
    }

    /**
     * Remove the specified budget.
     */
    public function destroy(Budget $budget)
    {
        // Guard: cek apakah budget masih ada dana terpakai/terikat
        if ($budget->used_amount > 0 || $budget->committed_budget > 0) {
            return redirect()->route('finance.budgets.index')
                ->with('error', 'Budget tidak dapat dihapus karena masih memiliki dana terpakai atau terikat. Nonaktifkan saja jika tidak digunakan lagi.');
        }

        $budget->delete();

        return redirect()->route('finance.budgets.index')
            ->with('success', 'Anggaran berhasil dihapus!');
    }
}
