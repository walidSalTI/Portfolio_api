<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;
class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        if ($projects->isEmpty()) {
            return response()->json([
                'message' => 'No projects found'
            ], 404);
        }
        $projects = $projects->map(function($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'image' => $project->image_url,
                'project_url' => $project->project_url,
                'started_at' => $project->started_at,
                'completed_at' => $project->completed_at,
                'display' => $project->display,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $projects,
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
            'project_url' => 'nullable|url',
            'started_at' => 'required|date',
            'completed_at' => 'nullable|date',
            'display' => 'nullable|boolean',
        ]);

        $portfolio = Portfolio::firstOrFail();
        $project = new Project();
        $project->title = Purify::clean($request->title);
        $project->description = Purify::clean($request->description);
        $project->project_url = $request->project_url ?? null;
        $project->started_at = $request->started_at;
        $project->completed_at = $request->completed_at ?? null;
        if(empty($project->title)||empty($project->description)){
            return response()->json([
                'message' => 'title or description cannot be empty after sanitization'
            ], 400);
        }
        if($request->has('display')){
            $project->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/projects/'), $imageName);
            $project->image = $imageName;
        }

        $project->portfolio_id = $portfolio->id;
        $project->save();

        return response()->json([
            'success' => true,
            'data' => $project
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $project_id)
    {
        $project = Project::where('id', $project_id)->first();
        if (!$project) {
            return response()->json([
                'message' => 'There is no such project'
            ], 404);
        }
        $project = [
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'image' => $project->image_url,
            'project_url' => $project->project_url,
            'started_at' => $project->started_at,
            'completed_at' => $project->completed_at,
            'display' => $project->display,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        ];
        return response()->json([
            'success' => true,
            'data' => $project
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , string $project_id)
    {
        $project = Project::where('id', $project_id)->first();
        if (!$project) {
            return response()->json([
                'message' => 'There is no such project to update'
            ], 404);
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'project_url' => 'nullable|url',
            'started_at' => 'required|date',
            'completed_at' => 'nullable|date',
            'display' => 'nullable|boolean',
        ]);
        $project->title = empty($cleaned_title = Purify::clean($request->title))? $project->title :$cleaned_title  ;
        $project->description = empty($cleaned_description = Purify::clean($request->description))? $project->description :$cleaned_description;
        $project->project_url = $request->project_url ?? $project->project_url;
        $project->started_at = $request->started_at ?? $project->started_at;
        $project->completed_at = $request->completed_at ?? $project->completed_at ?? null;
        if ($request->has('display')) {
            $project->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/projects/'), $imageName);
            if ($project->image) {
                @unlink(public_path('images/projects/' . $project->image));
            }
            $project->image = $imageName;
        }
        $project->save();
        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'Project updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $project_id)
    {
        $project = Project::where('id', $project_id)->first();
        if (!$project) {
            return response()->json([
                'message' => 'There is no such project to delete'
            ], 404); 
        }
        if ($project->image) {
            unlink(public_path('images/projects/' . $project->image));
        }
        $project->delete();
        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ], 200);
    }
}
