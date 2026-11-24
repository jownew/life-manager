<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * List the current user's tag names (used for suggestions).
     */
    public function index(Request $request)
    {
        return Tag::where('user_id', $request->user()->id)->orderBy('name')->pluck('name');
    }
}
