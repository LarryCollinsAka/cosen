<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    /**
     * Display a listing of the prompts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $prompts = Prompt::all();
        return view('admin.prompts.index', compact('prompts'));
    }

    /**
     * Show the form for creating a new prompt.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.prompts.create');
    }

    /**
     * Store a newly created prompt in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'incident_category' => 'required|string|max:255',
            'question' => 'required|string',
        ]);

        Prompt::create($request->all());

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt created successfully.');
    }

    /**
     * Show the form for editing the specified prompt.
     *
     * @param  \App\Models\Prompt  $prompt
     * @return \Illuminate\View\View
     */
    public function edit(Prompt $prompt)
    {
        return view('admin.prompts.edit', compact('prompt'));
    }

    /**
     * Update the specified prompt in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prompt  $prompt
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Prompt $prompt)
    {
        $request->validate([
            'incident_category' => 'required|string|max:255',
            'question' => 'required|string',
        ]);

        $prompt->update($request->all());

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt updated successfully.');
    }

    /**
     * Remove the specified prompt from storage.
     *
     * @param  \App\Models\Prompt  $prompt
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Prompt $prompt)
    {
        $prompt->delete();

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt deleted successfully.');
    }
}
