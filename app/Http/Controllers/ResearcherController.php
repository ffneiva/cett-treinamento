<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Researcher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResearcherController extends Controller
{
    public function index()
    {
        $researchers = Researcher::with('institution', 'works')->get();
        $institutions = Institution::all();
        return Inertia::render('Researchers/Index', [
            'researchers' => $researchers,
            'institutions' => $institutions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'birth_date' => 'required|date',
            'sex' => 'required',
            'cpf' => 'required',
            'university' => 'required',
            'institution.id' => 'required|exists:institutions,id',
        ], [
            'required' => 'O campo :attribute é obrigatório',
            'exists' => 'O campo :attribute selecionado não é valido',
        ]);

        $researcher = Researcher::create([
            'name' => $request['name'],
            'birth_date' => $request['birth_date'],
            'sex' => $request['sex'],
            'cpf' => $request['cpf'],
            'university' => $request['university'],
            'institution_id' => $request['institution']['id'],
        ]);

        // $researcher->institution()->attach();

        return redirect()->route('researcher.index')->with('response', $researcher);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'birth_date' => 'required|date',
            'sex' => 'required',
            'cpf' => 'required',
            'university' => 'required',
            'institution.id' => 'required|exists:institutions,id',
        ], [
            'required' => 'O campo :attribute é obrigatório',
            'exists' => 'O campo :attribute selecionado não é valido',
        ]);

        $researcher = Researcher::findOrFail($id);
        $researcher->name = $request->name;
        $researcher->birth_date = $request->birth_date;
        $researcher->sex = $request->sex;
        $researcher->cpf = $request->cpf;
        $researcher->university = $request->university;
        $researcher->institution_id = $request->institution['id'];
        $researcher->save();

        return redirect()->route('researcher.index')->with('response', $researcher);
    }

    public function destroy(string $id)
    {
        $researcher = Researcher::findOrFail($id);
        $researcher->delete();

        return redirect()->route('researcher.index')->with('response', $researcher);
    }
}
