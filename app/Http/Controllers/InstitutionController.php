<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::with('researchers', 'works')->get();
        return Inertia::render('Institutions/Index', [
            'institutions' => $institutions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fantasy_name' => 'required',
            'corporate_name' => 'required',
            'cnpj' => 'required',
            'type' => 'required',
        ], [
            'required' => 'O campo :attribute é obrigatório',
        ]);

        $institution = Institution::create([
            'fantasy_name' => $request['fantasy_name'],
            'corporate_name' => $request['corporate_name'],
            'cnpj' => $request['cnpj'],
            'type' => $request['type'],
        ]);

        return redirect()->route('institution.index')->with('response', $institution);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'fantasy_name' => 'required',
            'corporate_name' => 'required',
            'cnpj' => 'required',
            'type' => 'required',
        ], [
            'required' => 'O campo :attribute é obrigatório',
        ]);

        $institution = Institution::findOrFail($id);
        $institution->fantasy_name = $request->fantasy_name;
        $institution->corporate_name = $request->corporate_name;
        $institution->cnpj = $request->cnpj;
        $institution->type = $request->type;
        $institution->save();

        return redirect()->back()->with('response', $institution);
    }

    public function destroy(string $id)
    {
        $institution = Institution::findOrFail($id);
        $institution->delete();

        return redirect()->route('institution.index')->with('response', $institution);
    }
}
