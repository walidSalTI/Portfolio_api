<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;
class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skills = Skill::all();
        if ($skills->isEmpty()) {
            return response()->json([
                'message' => 'No skills found'
            ], 404);
        }
        $skills = $skills->map(function($skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name,
                'description' => $skill->description,
                'level' => $skill->level,
                'icon' => $skill->image_url,
                'display' => $skill->display,
                'created_at' => $skill->created_at,
                'updated_at' => $skill->updated_at,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $skills,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|integer|between:10,100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'display' => 'nullable|boolean',
        ]);
        $portfolio = Portfolio::firstOrFail();
        $skill = new Skill();
        $skill->name = Purify::clean($request->name);
        $skill->description = Purify::clean($request->description);
        $skill->level = $request->level;
        if(empty($skill->name)||empty($skill->description)){
            return response()->json([
                'message' => 'name or description cannot be empty after sanitization'
            ], 400);
        }
        if ($request->has('display')) {
            $skill->display = $request->display;
        }
        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/icons'), $imageName);
            $skill->icon = $imageName;
        }
        $skill->portfolio_id = $portfolio->id;
        $skill->save();
        return response()->json([
            'success' => true,
            'data' => $skill
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $skill_id)
    {
        $skill = Skill::where('id', $skill_id)->first();
        if (!$skill) {
            return response()->json([
                'message' => 'there is no such skill'
            ], 404);
        }
        $skill =[
                'id' => $skill->id,
                'name' => $skill->name,
                'description' => $skill->description,
                'level' => $skill->level,
                'icon' => $skill->image_url,
                'display' => $skill->display,
                'created_at' => $skill->created_at,
                'updated_at' => $skill->updated_at,
            ];
        return response()->json([
            'success' => true,
            'data' => $skill
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , string $skill_id)
    {
        $skill = Skill::where('id', $skill_id)->first();
        if (!$skill) {
            return response()->json([
                'message' => 'there is no such skill to update'
            ], 404);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|integer|between:10,100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'display' => 'nullable|boolean',
        ]);
        $skill->name = empty($cleaned_name = Purify::clean($request->name))? $skill->name :$cleaned_name  ;
        $skill->description = empty($cleaned_description = Purify::clean($request->description))? $skill->description :$cleaned_description;
        $skill->level = $request->level ?? $skill->level;
        if ($request->has('display')) {
            $skill->display = $request->display;
        }
        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/icons'), $imageName);
            if ($skill->image) {
                unlink(public_path('images/icons/' . $skill->icon));
            }
            $skill->icon = $imageName;
        }
        $skill->save();
        return response()->json([
            'success' => true,
            'data' => $skill,
            'message' => 'Skill section updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $skill_id)
    {
        $skill = Skill::where('id', $skill_id)->first();
        if (!$skill) {
            return response()->json([
                'message' => 'There is no such skill to delete'
            ], 404); 
        }
        if ($skill->image) {
            unlink(public_path('images/icons' . $skill->image));
        }
        $skill->delete();
        return response()->json([
            'success' => true,
            'message' => 'Skill deleted successfully'
        ], 200);
    }
}
