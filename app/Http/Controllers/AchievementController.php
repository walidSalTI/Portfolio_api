<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $achievements = Achievement::all();
        if ($achievements->isEmpty()) {
            return response()->json([
                'message' => 'No achievements found'
            ], 404);
        }
        $achievements = $achievements->map(function($achievement) {
            return [
                'id' => $achievement->id,
                'title' => $achievement->title,
                'description' => $achievement->description,
                'image' => $achievement->image_url,
                'achieved_at' => $achievement->achieved_at,
                'display' => $achievement->display,
                'created_at' => $achievement->created_at,
                'updated_at' => $achievement->updated_at,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $achievements,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico',
            'achieved_at' => 'required|date',
            'display' => 'nullable|boolean',
        ]);
        $portfolio = Portfolio::firstOrFail();
        $achievement = new Achievement();
        $achievement->title = Purify::clean($request->title);
        $achievement->description = Purify::clean($request->description);
        $achievement->achieved_at = $request->achieved_at;
        if(empty($achievement->title)||empty($achievement->description)){
            return response()->json([
                'message' => 'title or description cannot be empty after sanitization'
            ], 400);
        }
        if ($request->has('display')) {
            $achievement->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $achievement->image = $imageName;
        }
        $achievement->portfolio_id = $portfolio->id;
        $achievement->save();
        return response()->json([
            'success' => true,
            'data' => $achievement
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $achievement_id)
    {
        $achievement = Achievement::where('id', $achievement_id)->first();
        if (!$achievement) {
            return response()->json([
                'message' => 'There is no such achievement'
            ], 404);
        }
        $achievement = [
            'id' => $achievement->id,
            'title' => $achievement->title,
            'description' => $achievement->description,
            'image' => $achievement->image_url,
            'achieved_at' => $achievement->achieved_at,
            'display' => $achievement->display,
            'created_at' => $achievement->created_at,
            'updated_at' => $achievement->updated_at,
        ];
        return response()->json([
            'success' => true,
            'data' => $achievement
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , string $achievement_id)
    {
        $achievement = Achievement::where('id', $achievement_id)->first();
        if (!$achievement) {
            return response()->json([
                'message' => 'There is no such achievement to update'
            ], 404);
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'achieved_at' => 'required|date',
            'display' => 'nullable|boolean',
        ]);
        $achievement->title = empty($cleaned_title = Purify::clean($request->title))? $achievement->title :$cleaned_title  ;
        $achievement->description = empty($cleaned_description = Purify::clean($request->description))? $achievement->description :$cleaned_description;
        $achievement->achieved_at = $request->achieved_at ?? $achievement->achieved_at;
        if ($request->has('display')) {
            $achievement->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            if ($achievement->image) {
                unlink(public_path('images/' . $achievement->image));
            }
            $achievement->image = $imageName;
        }
        $achievement->save();
        return response()->json([
            'success' => true,
            'data' => $achievement,
            'message' => 'Achievement updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $achievement_id)
    {
        $achievement = Achievement::where('id', $achievement_id)->first();
        if (!$achievement) {
            return response()->json([
                'message' => 'There is no such achievement to delete'
            ], 404); 
        }
        if ($achievement->icon) {
            unlink(public_path('images/' . $achievement->icon));
        }
        $achievement->delete();
        return response()->json([
            'success' => true,
            'message' => 'Achievement deleted successfully'
        ], 200);
    }
}
