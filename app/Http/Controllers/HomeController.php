<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Text;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $news = Text::with(['category', 'user'])
            ->where('published', true)
            ->latest()
            ->get();

        return view('home', compact('news'));
    }

    public function categories(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('categories', compact('categories'));
    }

    public function showCategory(Category $category): View
    {
        $news = $category->texts()
            ->with('user')
            ->where('published', true)
            ->latest()
            ->get();

        return view('category', compact('category', 'news'));
    }
}
