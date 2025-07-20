<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Http\Requests\StoreItemsRequest;
use App\Http\Requests\StoreMenusCategoryRequest;
use App\Http\Requests\UpdateMenusCategoryRequest;
use App\Http\Resources\MenusCategoryResource;
use App\Models\Item;
use App\Models\MenuCategory;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenusCategoryController extends Controller
{
    use HttpResponses;
    

    public function index()
    {
        $categories = MenuCategory::all(); // Assuming you have a MenuCategory model

        return MenusCategoryResource::collection($categories);
    }

    // public function store(StoreMenusCategoryRequest $request)
    // {
    //     // Store the menu category in the database
    //   if ($request->hasFile('image')) {
    //         $file = $request->file('image'); // You have an UploadedFile instance
    //         $filename = time() . '.' . $file->getClientOriginalExtension();

    //         $filename = $file->storeAs('public/categories', $filename);        
    //         $category = MenuCategory::create([
    //             'name' => $request->name,
    //             'description' => $request->description,
    //             'image' => $filename,
    //         ]);
    //         if (!$category) {
    //             return response()->json([
    //                 'status' => 'Error has occurred...',
    //                 'message' => 'Cattegory created failed',
    //                 'data' => ''
    //             ], 500);
    //         }            
    //   }

    //     return new MenusCategoryResource($category);
    // }
    
    
    
    
    
    
//     public function store(StoreMenusCategoryRequest $request)
// {
//     $filename = null;

//     if ($request->hasFile('image')) {
//         $file = $request->file('image');
//         $filename = $file->storeAs('public/categories', time() . '.' . $file->getClientOriginalExtension());
//     }

//     $category = MenuCategory::create([
//         'name' => $request->name,
//         'description' => $request->description,
//         'image' => $filename,
//     ]);

//     if (!$category) {
//         return response()->json([
//             'status' => 'Error has occurred...',
//             'message' => 'Category creation failed',
//             'data' => ''
//         ], 500);
//     }

//     return new MenusCategoryResource($category);
// }








    // public function update(UpdateMenusCategoryRequest $request, $id)
    // {
    //     // Logic to update a menu category

    //     // Find the menu category and update it
    //     $menuCategory = MenuCategory::find($id);
    //     if (!$menuCategory) {
    //         return response()->json([
    //             'status' => 'Error has occurred...',
    //             'message' => 'No Category found',
    //             'data' => null
    //         ], 500);
    //     }        
    //   if ($request->hasFile('image')) {
    //         $file = $request->file('image'); // You have an UploadedFile instance
    //         $filename = time() . '.' . $file->getClientOriginalExtension();

    //         $filename = $file->storeAs('public/categories', $filename);        
    //         $menuCategory->update([
    //             'name' => $request->name,
    //             'description' => $request->description,
    //             'image' => $filename,
    //         ]);        
    //     }
    //     return new MenusCategoryResource($menuCategory);
    // }
    public function destroy($id)
    {
        // Logic to delete a menu category
        $menuCategory = MenuCategory::find($id);
        if (!$menuCategory) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'No Category found',
                'data' => null
            ], 500);
        }        
        $menuCategory->delete();

        return $this->success('','Menu category deleted successfully.');
    }
    
    



public function store(StoreMenusCategoryRequest $request)
{
    $filename = null;

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('categories'), $filename); // نخزن في public مباشرة
    }

    $category = MenuCategory::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $filename ? 'categories/' . $filename : null,  // ✅ إصلاح هنا
    ]);

    if (!$category) {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => 'Category creation failed',
            'data' => ''
        ], 500);
    }

    return new MenusCategoryResource($category);
}


    
    public function update(UpdateMenusCategoryRequest $request, $id)
{
    $menuCategory = MenuCategory::find($id);
    if (!$menuCategory) {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => 'No Category found',
            'data' => null
        ], 500);
    }

    if ($request->hasFile('image')) {
        // حذف الصورة القديمة (اختياري)
        if ($menuCategory->image && file_exists(public_path($menuCategory->image))) {
            // return response()->json(["image" => $menuCategory->image]);
            unlink(public_path($menuCategory->image));
        }

        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        // نحفظها في نفس مكان store()
        $file->move(public_path('categories'), $filename);

        // نحفظ المسار نفسه زي store()
        $menuCategory->image = 'categories/' . $filename;
    }

    // نحدّث الاسم والوصف دايمًا
    $menuCategory->name = $request->name;
    $menuCategory->description = $request->description;

    $menuCategory->save();

    return new MenusCategoryResource($menuCategory);
}

    
    
    
    
    public function show($id)
    {
        // Logic to display a single menu category
        $menuCategory = MenuCategory::find($id);
        if (!$menuCategory) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'No Category found',
                'data' => null
            ], 500);
        }        
        return new MenusCategoryResource($menuCategory);
    }

}
