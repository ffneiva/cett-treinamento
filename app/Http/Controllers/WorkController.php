<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Researcher;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class WorkController extends Controller
{
    public function index()
    {
        $institutions = Institution::all();
        $researchers = Researcher::all();
        $works = Work::with('researchers', 'institution')->get();
        return Inertia::render('Works/Index', [
            'institutions' => $institutions,
            'researchers' => $researchers,
            'works' => $works,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'thumb' => 'required|mimes:pdf,jpg,png|max:10000',
            'title' => 'required',
            'publication_date' => 'required|date',
            'institution.id' => 'required|exists:institutions,id',
            'file' => 'required|mimes:pdf,jpg,png',
            'grade' => 'required',
            'subject' => 'required',
            'type' => 'required',
            'language' => 'required',
            'license' => 'required',
        ], [
            'required' => 'O campo :atribute é obrigatório',
            'exists' => 'O campo :attribute selecionado não é valido',
        ]);

        $work = Work::create([
            'thumb' => $request['thumb']->store('thumbs'),
            'title' => $request['title'],
            'publication_date' => $request['publication_date'],
            'institution_id' => $request['institution.id'],
            'file' => $request['file']->store('works'),
            'grade' => $request['grade'],
            'subject' => $request['subject'],
            'type' => $request['type'],
            'language' => $request['language'],
            'license' => $request['license'],
        ]);

        $researchersIDs = array_map(function ($researcher) {
            return $researcher['id'];
        }, $request->input('researchers'));

        $work->researchers()->attach($researchersIDs);

        return redirect()->route('work.index')->with('work', $work);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'thumb' => 'nullable|mimes:pdf,jpg,png|max:10000',
            'title' => 'required',
            'publication_date' => 'required|date',
            'institution.id' => 'required|exists:institutions,id',
            'file' => 'nullable|mimes:pdf,jpg,png',
            'grade' => 'required',
            'subject' => 'required',
            'type' => 'required',
            'language' => 'required',
            'license' => 'required',
        ], [
            'required' => 'O campo :attribute é obrigatório',
            'exists' => 'O campo :attribute selecionado não é valido',
        ]);

        $work = Work::findOrFail($id);

        if ($request->file) {
            Storage::delete($work->file);
            $work->file = $request->file->store('works');
        }

        if ($request->thumb) {
            Storage::delete($work->thumb);
            $work->thumb = $request->thumb->store('thumbs');
        }

        $work->title = $request->title;
        $work->publication_date = $request->publication_date;
        $work->institution_id = $request->institution['id'];
        $work->grade = $request->grade;
        $work->subject = $request->subject;
        $work->type = $request->type;
        $work->language = $request->language;
        $work->license = $request->license;

        $researchersIDs = array_map(function ($researcher) {
            return $researcher['id'];
        }, $request->input('researchers'));

        $work->researchers()->sync($researchersIDs);

        $work->save();

        return redirect()->route('work.index')->with('work', $work);
    }

    public function destroy(string $id)
    {
        $work = Work::findOrFail($id);

        if ($work->file) {
            Storage::delete($work->file);
        }

        if ($work->thumb) {
            Storage::delete($work->thumb);
        }

        $work->researchers()->detach();

        $work->delete();

        return redirect()->route('work.index')->with('work', $work);
    }
}
