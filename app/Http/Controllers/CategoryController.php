<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        if (request()->wantsJson()) {
            return $categories;
        }

        return Inertia::render('Categories/Index', [
            'items' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create(
            $request->validate([
                'name' => ['required'],
                'budget' => ['required', 'numeric'],
            ])
        );

        return redirect()->back()->with('message', "$category->name added successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Inertia\Response
     */
    public function show(Category $category, string $id)
    {
        $category = Category::findOrFail($id);

        if (request()->wantsJson()) {
            return $category;
        }

        return Inertia::render('Categories/Show', [
            'item' => $category
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);

        $category->update($request->all());

        return redirect()->back()->with('message', "$category->name updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->expenses()->count() > 0) {
            return redirect()->back()->with('message', "$category->name delete failed due associated exepnses.");
        }

        if ($category->delete()) {
            return redirect()->back()->with('message', "$category->name deleted successfully");
        }

        return redirect()->back()->with('message', "$category->name delete failed due an error.");
    }
}
