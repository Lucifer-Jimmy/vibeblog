<?php

namespace App\Http\Controllers;

use App\Models\Link;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::visible()
            ->orderBy('created_at')
            ->paginate(12);

        return view('links.index', compact('links'));
    }
}
