<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;

class CustomerFaqController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $query = Faq::active();

        if ($category && array_key_exists($category, Faq::categories())) {
            $query->where('category', $category);
        }

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('question', 'like', "%{$s}%")
                  ->orWhere('answer', 'like', "%{$s}%");
            });
        }

        $faqs = $query->get();
        $categories = Faq::categories();
        $currentCategory = $category;

        return view('shop.faq', compact('faqs', 'categories', 'currentCategory'));
    }
}
