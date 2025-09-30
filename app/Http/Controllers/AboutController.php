<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

class AboutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $about = About::first();
        if (!isset($about)) {
            return response()->json([
                'message' => 'No about section found'
            ], 404);
        }
        $about=[
            'id' => $about->id,
            'description' => $about->description,
            'image_url' => $about->image_url,
            'display' => $about->display,
            'created_at' => $about->created_at,
            'updated_at' => $about->updated_at,
        ];
        return response()->json([
            'success' => true,
            'data' => $about
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (About::first()) {
            return response()->json([
                'message' => 'About section already exists'
            ], 400);
        }
        $request->validate([
            'description' => 'required|string',
            'display' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
        ]);

        $portfolio = Portfolio::firstOrFail();
        $about = new About();
        $about->description = Purify::clean($request->description);
        if(empty($about->description)){
            return response()->json([
                'message' => 'Description cannot be empty after sanitization'
            ], 400);
        }
        if ($request->has('display')) {
            $about->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $about->image = $imageName;
        }
        $about->portfolio_id = $portfolio->id;
        $about->save();
        return response()->json([
            'success' => true,
            'data' => $about
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(About $about)
    {
        //empty for now
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (About::first() === null) {
            return response()->json([
                'message' => 'No about section found in update'
            ], 404);
        }
        $about = About::first();
        $request->validate([
            'description' => 'required|string',
            'display' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'

        ]);
        $about->description = empty($cleaned_description = Purify::clean($request->description))? $about->description :$cleaned_description;
        if ($request->has('display')) {
            $about->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            if ($about->image) {
                unlink(public_path('images/' . $about->image));
            }
            $about->image = $imageName;
        }
        $about->save();
        return response()->json([
            'success' => true,
            'data' => $about,
            'message' => 'About section updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(About $about)
    {
        if (About::first() === null) {
            return response()->json([
                'message' => 'No about section found to delete'
            ], 404); 
        }
        $about = About::first();
        if ($about->image) {
            unlink(public_path('images/' . $about->image));
        }
        if ($about->cv) {
            unlink(public_path('CV/' . $about->cv));
        }
        $about->delete();
        return response()->json([
            'success' => true,
            'message' => 'About section deleted successfully'
        ], 200);
    }
}
