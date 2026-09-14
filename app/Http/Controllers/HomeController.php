<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Section;
use Anthropic\Client;
class HomeController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('order_index')->get();

        $grouped = $sections->mapWithKeys(function ($s) {
            return [$s->slug => [
                'section' => $s,
                'items' => Content::where('section_id', $s->id)
                    ->where('published', true)
                    ->orderByDesc('updated_at')
                    ->limit(12)
                    ->get(),
            ]];
        });

        return view('home', compact('grouped'));
    }

    public function section($slug)
    {
        $section = Section::where('slug', $slug)->firstOrFail();
        $items = Content::where('section_id', $section->id)
            ->where('published', true)
            ->orderByDesc('updated_at')
            ->get();

        return view('section', compact('section', 'items'));
    }
    public function testClaude()
    {
        // The client automatically reads the key from your .env file
        $client = new Client();

        $message = $client->messages->create(
            maxTokens: 256,
            messages: [
                ['role' => 'user', 'content' => 'Say hello from the Guilford Hub!']
            ],
            model: 'claude-sonnet-4-5-20250929', // Use a current model ID
        );

        // The response text is in the first content block
        $reply = $message->content[0]->text;

        return response()->json(['claude_reply' => $reply]);
    }
}