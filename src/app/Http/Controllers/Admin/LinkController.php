<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::orderBy('created_at')->get();

        return view('admin.links.index', compact('links'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'url' => 'required|url|max:255',
            'avatar' => 'nullable|url|max:255',
            'status' => 'required|in:visible,hidden',
        ]);

        Link::create($validated);

        return back()->with('success', '友链添加成功');
    }

    public function update(Request $request, Link $link)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'url' => 'required|url|max:255',
            'avatar' => 'nullable|url|max:255',
            'status' => 'required|in:visible,hidden',
        ]);

        $link->update($validated);

        return back()->with('success', '友链更新成功');
    }

    public function destroy(Link $link)
    {
        $link->delete();

        return back()->with('success', '友链已删除');
    }
}
