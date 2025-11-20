<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CategoryDataTable;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $obj;

    public function __construct()
    {
        $this->obj = new Category();
    }

    public function index(CategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.category.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->obj->whereNull('parent_id')->with('allChildren')->get();

        return view('admin.category.create', [
            'obj' => $this->obj,
            'categories' => $categories
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
  public function store(CategoryRequest $request)
    {
        try {
            $data = $request->validated();

            $data['status'] = $data['status'] ?? 1;

            $data['parent_id'] = $data['parent_id'] ?? null;

            $category = $this->obj->create($data);

            return redirect()->route('categories.create')
                            ->with('success', 'Category added successfully.');
        } catch (\Exception $e) {
            \Log::error("Category creation error: ".$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->withErrors('Something went wrong. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

  public function edit($id)
    {
        try {
            $id = decrypt($id);

            $obj = $this->obj->findOrFail($id);

            $categories = $this->obj->whereNull('parent_id')->with('allChildren')->get();

            return view('admin.category.create', compact('obj', 'categories'));

        }  catch (\Exception $e) {
            return redirect()->route('categories.index')->withErrors('Category not found.');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
