<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index(Request $request)
    {
        $query = Specialization::withCount(['instructors', 'subjects']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $specializations = $query->orderBy('name')->paginate(20);

        return view('admin.specializations.index', compact('specializations'));
    }

    public function create()
    {
        return view('admin.specializations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:specializations,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        try {
            $specialization = Specialization::create($validated);

            AuditLog::log('specialization_created', $specialization);

            return redirect()
                ->route('admin.specializations.index')
                ->with('success', 'Specialization created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create specialization: ' . $e->getMessage());
        }
    }

    public function show(Specialization $specialization)
    {
        $specialization->load(['instructors', 'subjects']);
        return view('admin.specializations.show', compact('specialization'));
    }

    public function edit(Specialization $specialization)
    {
        return view('admin.specializations.edit', compact('specialization'));
    }

    public function update(Request $request, Specialization $specialization)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:specializations,code,' . $specialization->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        try {
            $oldValues = $specialization->toArray();
            $specialization->update($validated);

            AuditLog::log('specialization_updated', $specialization, $oldValues, $specialization->toArray());

            return redirect()
                ->route('admin.specializations.index')
                ->with('success', 'Specialization updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update specialization: ' . $e->getMessage());
        }
    }

    public function destroy(Specialization $specialization)
    {
        try {
            if ($specialization->instructors()->count() > 0) {
                return back()->with('error', 'Cannot delete specialization with assigned instructors.');
            }

            AuditLog::log('specialization_deleted', $specialization);
            $specialization->delete();

            return redirect()
                ->route('admin.specializations.index')
                ->with('success', 'Specialization deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete specialization: ' . $e->getMessage());
        }
    }

    public function trashed()
    {
        $specializations = Specialization::onlyTrashed()
            ->withCount(['instructors', 'subjects'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.specializations.trashed', compact('specializations'));
    }

    public function restore($id)
    {
        try {
            $specialization = Specialization::onlyTrashed()->findOrFail($id);
            $specialization->restore();

            AuditLog::log('specialization_restored', $specialization);

            return redirect()
                ->route('admin.specializations.index')
                ->with('success', 'Specialization restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore specialization: ' . $e->getMessage());
        }
    }
}