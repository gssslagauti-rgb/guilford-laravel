<?php

namespace App\Http\Controllers;

use App\Models\FacebookPost;
use Illuminate\Http\Request;
use Anthropic\Client as AnthropicClient;

class FacebookController extends Controller
{
    // -----------------------------
    // Admin list + form
    // -----------------------------
    public function index()
    {
        if (!auth()->user()->isModerator()) {
            abort(403);
        }

        $posts = FacebookPost::orderByDesc('created_at')->get();
        $sources = FacebookPost::SOURCES;

        return view('admin.facebook', compact('posts', 'sources'));
    }

    // -----------------------------
    // Save a new Facebook post
    // -----------------------------
    public function store(Request $request)
    {
        if (!auth()->user()->isModerator()) {
            abort(403);
        }

        $data = $request->validate([
            'source_page'   => 'required|string',
            'original_text' => 'required|string|min:5',
            'post_url'      => 'nullable|url',
            'post_date'     => 'nullable|date',
            'summary'       => 'nullable|string',
        ]);

        $data['added_by'] = auth()->user()->display_name;
        $data['published'] = $request->has('published');

        FacebookPost::create($data);

        return back()->with('success', 'Facebook post saved.');
    }

    // -----------------------------
    // Summarize with Claude (optional)
    // -----------------------------
    public function summarize(Request $request)
    {
        if (!auth()->user()->isModerator()) {
            abort(403);
        }

        $request->validate([
            'text' => 'required|string|min:20',
            'source' => 'required|string',
        ]);

        try {
            $client = new AnthropicClient();

            $prompt = "You are a local news editor for Guilford, Connecticut. "
                . "Rewrite the following Facebook post from \"{$request->source}\" "
                . "into a clean, factual 2-3 sentence announcement suitable for a town community site. "
                . "Keep it neutral, remove emojis and hashtags, and preserve important dates, locations, and details. "
                . "Return only the rewritten text, nothing else.\n\n"
                . "Original post:\n{$request->text}";

            $message = $client->messages->create(
                maxTokens: 400,
                messages: [
                    ['role' => 'user', 'content' => $prompt]
                ],
                model: 'claude-haiku-4-5-20251001',
            );

            $summary = $message->content[0]->text ?? '';

            return response()->json([
                'summary' => trim($summary),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // -----------------------------
    // Delete a post
    // -----------------------------
    public function destroy(FacebookPost $post)
    {
        if (!auth()->user()->isModerator()) {
            abort(403);
        }

        $post->delete();
        return back()->with('success', 'Post deleted.');
    }

    // -----------------------------
    // Public listing (homepage + section page)
    // -----------------------------
    public function publicList()
    {
        return FacebookPost::where('published', true)
            ->orderByDesc('post_date')
            ->orderByDesc('created_at')
            ->paginate(20);
    }
}