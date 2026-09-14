<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Section;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class ContributeController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'section' => 'required|string',
            'title' => 'required|string|max:255',
        ]);

        $section = Section::where('slug', $request->section)->firstOrFail();

        $data = [];
        foreach (['title', 'body', 'date', 'location', 'season', 'registration', 'category', 'bullets'] as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->input($field);
            }
        }

        Contribution::create([
            'section_id' => $section->id,
            'user_id' => auth()->id(),
            'contribution_type' => 'new',
            'content_json' => json_encode($data),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thanks! Your contribution is awaiting moderator approval.');
    }

    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Subscriber::firstOrCreate(['email' => strtolower($request->email)]);
        return back()->with('success', 'You are subscribed!');
    }
}