<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\_core\DashboardController;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\ForumMessage;
use Illuminate\Http\Request;

class ForumController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
        $this->setTitle('Forum');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Forum', '#');
    }

    /**
     * Display the forum thread.
     */
    public function index()
    {
        $messages = ForumMessage::with('user')
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        $this->setData('messages', $messages);

        return view('dashboard.forum.index', $this->data);
    }

    /**
     * Store a newly posted message.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'message' => ['required', 'string', 'max:2000'],
            ]);

            $message = ForumMessage::create([
                'user_id' => $request->user()->id,
                'message' => $validated['message'],
            ]);

            $message->load('user');

            return (new SuccessResource('Pesan terkirim', [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at->format('d M Y H:i'),
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'photo_url' => $message->user->photo_url,
                ],
            ]))
                ->response()
                ->setStatusCode(201);
        } catch (\Illuminate\Validation\ValidationException $th) {
            return (new ErrorResource($th, 'Pesan tidak boleh kosong'))
                ->response()
                ->setStatusCode(422);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Poll for new messages after a given id (used for lightweight auto-refresh).
     */
    public function poll(Request $request)
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = ForumMessage::with('user')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at->format('d M Y H:i'),
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'photo_url' => $message->user->photo_url,
                    ],
                ];
            });

        return (new SuccessResource('OK', $messages))
            ->response()
            ->setStatusCode(200);
    }
}
