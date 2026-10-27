<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Role;
use App\Models\Text;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $texts = Text::with(['category', 'user'])->latest()->get();
        $users = User::with('role')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $roles = Role::orderBy('id')->get();

        return view('admin', compact('categories', 'roles', 'texts', 'users'));
    }

    public function updateText(Request $request, Text $text): RedirectResponse
    {
        $validated = $request->validate([
            'heading' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $text->update($validated);

        return redirect()->route('admin')->with('status', 'Статья обновлена.');
    }

    public function deleteText(Text $text): RedirectResponse
    {
        $text->delete();

        return redirect()->route('admin')->with('status', 'Статья удалена.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user->role()->associate(Role::findOrFail($validated['role_id']));
        $user->save();

        return redirect()->route('admin')->with('status', 'Роль изменена.');
    }

    public function updateBlock(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'is_blocked' => ['required', 'boolean'],
        ]);

        $user->is_blocked = $validated['is_blocked'];
        $user->save();

        return redirect()->route('admin')->with('status', 'Статус пользователя изменен.');
    }
}
