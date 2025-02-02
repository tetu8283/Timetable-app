<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;

class SubjectController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $subjects = Subject::orderBy('subject_code', 'asc')->get();

        return view('subjects.SubjectIndex', compact('subjects'));
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // バリデーションを追加
        $request->validate([
            'subject_code' => 'required|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:255',
            'school_id' => 'required|string',
            'color' => 'required|string',
        ]);

        $subject = new Subject();
        $subject->subject_code = $request->subject_code;
        $subject->subject_name = $request->subject_name;
        $subject->school_id = $request->school_id;
        $subject->color = $request->color;

        $subject->save();

        return redirect()->route('subject.index')->with('success', '科目が作成されました。');
    }

    /**
     * Summary of edit
     * @param \App\Models\Subject $subject
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Subject $subject)
    {
        $subject = Subject::find($subject->id);
        return view('subjects.SubjectEdit', compact('subject'));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Subject $subject
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Subject $subject)
    {
        $subject = Subject::find($subject->id);
        $subject->subject_name = $request->subject_name;
        $subject->school_id = $request->school_id;
        $subject->color = $request->color;

        $subject->save();

        return redirect()->route('subject.index');
    }

    /**
     * Summary of destroy
     * @param \App\Models\Subject $subject
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Subject $subject)
    {
        $subject = Subject::find($subject->id);

        $subject->delete();

        return redirect()->route('subject.index');
    }
}
