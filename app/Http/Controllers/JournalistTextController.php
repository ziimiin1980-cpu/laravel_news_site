<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class JournalistTextController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $texts = $request->user()->texts()->with('category')->latest()->get();

        return view('journalist', compact('categories', 'texts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'heading' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $request->user()->texts()->create([
            ...$validated,
            'published' => true,
        ]);

        return redirect()->route('journalist')->with('status', 'Статья опубликована.');
    }

    public function edit(Request $request, Text $text): View
    {
        $this->checkOwnership($request, $text);

        $categories = Category::orderBy('name')->get();

        return view('journalist_edit', compact('categories', 'text'));
    }

    public function update(Request $request, Text $text): RedirectResponse
    {
        $this->checkOwnership($request, $text);

        $validated = $request->validate([
            'heading' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $text->update($validated);

        return redirect()->route('journalist')->with('status', 'Статья обновлена.');
    }

    private function checkOwnership(Request $request, Text $text): void
    {
        abort_unless(
            (int) $text->user_id === (int) $request->user()->getAuthIdentifier(),
            403
        );
    }
}
