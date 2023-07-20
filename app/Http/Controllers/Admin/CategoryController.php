<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() {
        $pageTitle  = "Categories";
        $categories = Category::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.category.index', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'name' => 'required',
        ]);

        if ($id == 0) {
            $category     = new Category();
            $notification = 'Category added successfully.';
        } else {
            $category     = Category::findOrFail($id);
            $notification = 'Category updated successfully';
        }

        $category->name = $request->name;
        $category->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Category::changeStatus($id);
    }

    public function subcategory() {
        $pageTitle     = "Sub Categories";
        $categories    = Category::active()->orderBy('id', 'desc')->get();
        $subcategories = SubCategory::orderBy('id', 'desc')->with('category')->paginate(getPaginate());

        return view('admin.category.subcategory', compact('pageTitle', 'subcategories', 'categories'));
    }

    public function subCategoryStore(Request $request, $id = 0) {
        $request->validate([
            'name'        => 'required',
            'category_id' => 'required',
        ]);

        if ($id == 0) {
            $subcategory  = new SubCategory();
            $notification = 'Subcategory added successfully.';
        } else {
            $subcategory         = SubCategory::findOrFail($id);
            $subcategory->status = $request->status ? 1 : 0;
            $notification        = 'Subcategory updated successfully';
        }

        $subcategory->name        = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function subcategoryStatus($id) {
        return SubCategory::changeStatus($id);
    }

}
