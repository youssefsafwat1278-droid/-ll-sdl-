<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('author')->latest()->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_featured' => 'nullable|boolean',
        ]);

        $imageUrl = null;

        // Upload image if provided
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'news_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/news'), $filename);
            $imageUrl = '/images/news/' . $filename;
        }

        News::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imageUrl,
            'is_featured' => $request->has('is_featured'),
            'author_id' => auth()->id(),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'تم إضافة الخبر بنجاح');
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_featured' => 'nullable|boolean',
        ]);

        $news = News::findOrFail($id);

        $imageUrl = $news->image_url;

        // Upload new image if provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($news->image_url && file_exists(public_path($news->image_url))) {
                unlink(public_path($news->image_url));
            }

            $file = $request->file('image');
            $filename = 'news_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/news'), $filename);
            $imageUrl = '/images/news/' . $filename;
        }

        $news->update([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imageUrl,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'تم تحديث الخبر بنجاح');
    }

    public function destroy($id)
    {
        News::destroy($id);
        return back()->with('success', 'تم حذف الخبر بنجاح');
    }
    public function toggleFeatured($id)
    {
        $news = News::findOrFail($id);
        $news->update(['is_featured' => !$news->is_featured]);

        return back()->with('success', 'تم تحديث حالة الخبر');
    }
}
