<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Http\Requests\StoreItemsRequest;
use App\Http\Requests\UpdateItemsRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\MenuCategory;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ItemsController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $items = Item::with('menuCategory')->get(); // Assuming you have an Item model

        return ItemResource::collection($items);
    }

    // public function store(StoreItemsRequest $request)
    // {
    //     // dd($request->all());
    //   if ($request->hasFile('image')) {
    //         $file = $request->file('image'); // You have an UploadedFile instance
    //         $filename = $file->storeAs('public/item', time() . '.' . $file->getClientOriginalExtension());

    //         // $file->storeAs('storage/items', $filename);

    //         $item = Item::create([
    //             'name' => $request->name,
    //             'description' => $request->description,
    //             'price' => $request->price,
    //             'image' => $filename,
    //             'menucategory_id' =>$request->category_id,
    //             'type' => $request->type
    //         ]);
    //     }
    //     if (!$item) {
    //         return response()->json([
    //             'status' => 'Error has occurred...',
    //             'message' => 'Item creation failed',
    //             'data' => null
    //         ], 500);
    //     }

    //     $item = new ItemResource($item);
    //     return response()->json([
    //             'message' => 'item created Successfully',
    //             'data' => $item            
    //     ]);
    // }
    
    
    
// public function update(UpdateItemsRequest $request, $id)
// {
//     $item = Item::find($id);
//         if (!$item)
//          {
//             return response()->json([
//             'status' => 'Error has occurred...',
//             'message' => 'No items Found',
//             'data' => null
//             ], 500);
//          } 

//     if ($request->hasFile('image')) {
//         $file = $request->file('image');
//         $filename = time() . '.' . $file->getClientOriginalExtension();
//         $file->storeAs('public/items', $filename);
//         $item->image = $filename;
//         $item->name = $request->name;
//         $item->description = $request->description;
//         $item->price = $request->price;
//         $item->menucategory_id = $request->category_id;
//     }
//     if (!$item->save()) {
//         return response()->json([
//                 'status' => 'Error has occurred...',
//                 'message' => 'Item update failed',
//                 'data' => null
//             ], 500);;
//     }

//         $item = new ItemResource($item);
//         return response()->json([
//                 'message' => 'item Updated Successfully',
//                 'data' => $item            
//         ]);
// }

    

public function store(StoreItemsRequest $request)
{
    $filename = null;

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        // نحفظ الصورة في مجلد public/item مباشرة
        $file->move(public_path('item'), $filename);
    }

    $item = Item::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'image' => 'item/' . $filename, // بدون storage
        'menucategory_id' => $request->category_id,
        'type' => $request->type
    ]);

    if (!$item) {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => 'Item creation failed',
            'data' => null
        ], 500);
    }

    return response()->json([
        'message' => 'Item created successfully',
        'data' => new ItemResource($item)
    ]);
}




public function update(UpdateItemsRequest $request, $id)
{
    $item = Item::find($id);

    if (!$item) {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => 'No items Found',
            'data' => null
        ], 500);
    }

    if ($request->hasFile('image')) {
        // حذف الصورة القديمة لو موجودة
        if ($item->image && file_exists(public_path($item->image))) {
            unlink(public_path($item->image));
        }

        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        // نحفظ الصورة في نفس المجلد زي store()
        $file->move(public_path('item'), $filename);

        // نسجل نفس المسار زي ما في store
        $item->image = 'item/' . $filename;
    }

    // نحدث باقي البيانات
    $item->name = $request->name;
    $item->description = $request->description;
    $item->price = $request->price;
    $item->menucategory_id = $request->category_id;
    $item->type = $request->type;

    if (!$item->save()) {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => 'Item update failed',
            'data' => null
        ], 500);
    }

    return response()->json([
        'message' => 'Item updated successfully',
        'data' => new ItemResource($item)
    ]);
}




    public function destroy($id)
    {
        // Logic to delete a menu category
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'No items Found',
                'data' => null
            ], 500);
        }        
        $item->delete();

        return $this->success('','item deleted successfully');
    }
    public function show($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'No items Found',
                'data' => null
            ], 500);
        }

        return new ItemResource($item);
    }
}