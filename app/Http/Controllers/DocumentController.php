<?php

namespace App\Http\Controllers;

use App\Domain\Documents\CommentAdded;
use App\Domain\Documents\DocumentCreated;
use App\Domain\Documents\DocumentRenamed;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use InertiaLive\InertiaLive;

use function InertiaLive\live;

class DocumentController extends Controller
{
    private function routeName(string $name): string
    {
        return (request()->routeIs('react.*') ? 'react.' : '').$name;
    }

    public function index(): Response
    {
        return Inertia::render('Documents/Index', [
            'documents' => Document::latest()->get(['uuid', 'title']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:120']]);
        $uuid = (string) Str::uuid();

        event(new DocumentCreated($uuid, $data['title']));

        return to_route($this->routeName('documents.show'), $uuid);
    }

    public function show(Document $document): Response
    {
        $topic = "documents.{$document->uuid}";

        return InertiaLive::render('Documents/Show', [
            'document' => live($topic, $document->only('uuid', 'title')),
            'comments' => live($topic, fn () => $document->comments()->latest('id')->get(['id', 'body'])),
        ]);
    }

    public function rename(Request $request, Document $document): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:120']]);

        event(new DocumentRenamed($document->uuid, $data['title']));

        return back();
    }

    public function comment(Request $request, Document $document): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:500']]);

        event(new CommentAdded($document->uuid, $data['body']));

        return back();
    }
}
