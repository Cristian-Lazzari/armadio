<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /** Elenco programmazioni salvate (esclude l'autosave). */
    public function index()
    {
        return Plan::where('name', '!=', Plan::AUTO)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /** Stato corrente (autosave). Ritorna null se non esiste ancora. */
    public function auto()
    {
        $plan = Plan::where('name', Plan::AUTO)->first();
        return response()->json($plan?->state);
    }

    /** Aggiorna l'autosave (chiamato in debounce dal frontend). */
    public function saveAuto(Request $request)
    {
        $data = $request->validate(['state' => 'required|array']);
        Plan::updateOrCreate(['name' => Plan::AUTO], ['state' => $data['state']]);
        return response()->json(['ok' => true]);
    }

    /** Salva o sovrascrive una programmazione con nome. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'state' => 'required|array',
        ]);
        if ($data['name'] === Plan::AUTO) {
            abort(422, 'Nome riservato.');
        }
        $plan = Plan::updateOrCreate(['name' => $data['name']], ['state' => $data['state']]);
        return response()->json(['id' => $plan->id, 'name' => $plan->name]);
    }

    public function show(Plan $plan)
    {
        return response()->json(['id' => $plan->id, 'name' => $plan->name, 'state' => $plan->state]);
    }

    public function destroy(Plan $plan)
    {
        if ($plan->name === Plan::AUTO) {
            abort(422, 'Non eliminabile.');
        }
        $plan->delete();
        return response()->json(['ok' => true]);
    }
}
