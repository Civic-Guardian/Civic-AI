<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    /**
     * GET /hazards/{id}/comments
     * Retrieve discussion thread comments for a hazard.
     */
    public function index($hazardId)
    {
        $comments = Comment::where('hazard_id', $hazardId)->orderBy('created_at', 'desc')->get();

        if ($comments->isEmpty()) {
            // Seed default comments if none exist yet for this hazard
            $defaultComments = [
                [
                    'hazard_id' => $hazardId,
                    'user_name' => 'Municipal Corporation',
                    'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
                    'is_official' => true,
                    'created_at' => now()->subMinutes(10),
                    'updated_at' => now()->subMinutes(10),
                ],
                [
                    'hazard_id' => $hazardId,
                    'user_name' => 'Ramesh Kumar',
                    'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
                    'is_official' => false,
                    'created_at' => now()->subHours(1),
                    'updated_at' => now()->subHours(1),
                ]
            ];

            foreach ($defaultComments as $def) {
                Comment::create($def);
            }

            $comments = Comment::where('hazard_id', $hazardId)->orderBy('created_at', 'desc')->get();
        }

        $formatted = $comments->map(function ($c) {
            return [
                'id' => $c->id,
                'hazard_id' => $c->hazard_id,
                'user_name' => $c->user_name,
                'content' => $c->content,
                'is_official' => (bool) $c->is_official,
                'created_at' => $c->created_at ? $c->created_at->diffForHumans() : 'Just now',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    /**
     * POST /hazards/{id}/comments
     * Post a new comment.
     */
    public function store(Request $request, $hazardId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'hazard_id' => $hazardId,
            'user_id' => auth()->id(),
            'user_name' => $request->input('user_name', auth()->user()?->name ?? 'Citizen'),
            'content' => $request->input('content'),
            'is_official' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $comment->id,
                'hazard_id' => $comment->hazard_id,
                'user_name' => $comment->user_name,
                'content' => $comment->content,
                'is_official' => false,
                'created_at' => 'Just now',
            ]
        ], 201);
    }

    /**
     * DELETE /comments/{id}
     * Deletes a specific comment.
     */
    public function destroy($id)
    {
        Comment::destroy($id);
        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
