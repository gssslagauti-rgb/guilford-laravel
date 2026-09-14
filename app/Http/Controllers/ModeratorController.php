<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Contribution;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    public function dashboard()
    {
        $pending = Contribution::with(['user', 'section'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        return view('moderator.dashboard', compact('pending'));
    }

    public function moderate(Request $request, Contribution $contribution)
    {
        $request->validate(['action' => 'required|in:approved,rejected']);

        $contribution->update([
            'status' => $request->action,
            'moderator_id' => auth()->id(),
            'moderator_note' => $request->note ?? '',
            'reviewed_at' => now(),
        ]);

        if ($request->action === 'approved') {
            $data = $contribution->content;

            $meta = [];
            foreach (['date', 'location', 'season', 'registration', 'category'] as $field) {
                if (!empty($data[$field])) {
                    $meta[$field] = $data[$field];
                }
            }
            if (!empty($data['bullets'])) {
                $meta['bullets'] = array_values(array_filter(array_map('trim', explode("\n", $data['bullets']))));
            }

            // Safe contributor name — falls back if user was deleted
            $contributorName = optional($contribution->user)->display_name ?? 'Anonymous';

            if ($contribution->contribution_type === 'edit' && $contribution->target_id) {
                $content = Content::find($contribution->target_id);
                if ($content) {
                    $content->update([
                        'title' => $data['title'] ?? $content->title,
                        'body' => $data['body'] ?? $content->body,
                        'metadata_json' => json_encode($meta),
                        'contributor_name' => $contributorName,
                    ]);
                }
            } else {
                Content::create([
                    'section_id' => $contribution->section_id,
                    'title' => $data['title'] ?? 'Untitled',
                    'body' => $data['body'] ?? '',
                    'metadata_json' => json_encode($meta),
                    'contributor_id' => $contribution->user_id,
                    'contributor_name' => $contributorName,
                    'published' => true,
                ]);
            }
        }

        return back()->with('success', "Contribution {$request->action}.");
    }
}