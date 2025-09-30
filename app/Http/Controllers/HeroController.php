<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

class HeroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hero = Hero::first();
        if(!isset($hero)){
            return response()->json([
                'message' => 'No hero section found'
            ],404);
        }
        $data = [
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'image' => $hero->image_url,
            'cv' => $hero->cv_url,
            'display' => $hero->display,
            'created_at' => $hero->created_at,
            'updated_at' => $hero->updated_at,
        ];
        return response()->json([
            'success' => true,
            'data' => $data
        ],200);
    }   


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(Hero::first()){
            return response()->json([
                'message' => 'Hero section already exists'
            ],400);
        }
        $request->validate([    
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',       
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cv' => 'nullable|file|mimes:pdf',
            'display' => 'nullable|boolean',
        ]);
        $portfolio = Portfolio::firstOrFail();
        $hero = new Hero();
        $hero->title = empty($cleaned_title = Purify::clean($request->title))? $hero->title :$cleaned_title  ;
        $hero->subtitle = empty($cleaned_subtitle = Purify::clean($request->subtitle))? $hero->subtitle :$cleaned_subtitle;
        if($request->has('display')){
            $hero->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            $hero->image = $imageName;
        }
        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $cvName = time().'.'.$cv->getClientOriginalExtension();
            $request->cv->move(public_path('CV'), $cvName);
            $hero->cv = $cvName;
        }
        $hero->portfolio_id = $portfolio->id;
        $hero->save();
        return response()->json([
            'success' => true,
            'data' => $hero
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hero $hero)
    {
        //empty for now
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Hero::first() === null) {
            return response()->json([
                'message' => 'No hero section found in update'
            ],404);
        }
        $hero = Hero::first();
        $request->validate([    
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',       
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cv' => 'nullable|file|mimes:pdf',
            'display' => 'nullable|boolean',
        ]);
        $hero->title = Purify::clean($request->title) ?? $hero->title;
        $hero->subtitle = Purify::clean($request->subtitle) ?? $hero->subtitle;
        if (empty($hero->title) || empty($hero->subtitle)) {
            return response()->json([
                'message' => 'Title and subtitle cannot be empty after sanitization'
            ], 400);
        }   
        if($request->has('display')){
            $hero->display = $request->display;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            if($hero->image){
                unlink(public_path('images/'.$hero->image));
            }
            $hero->image = $imageName;
        }
        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $cvName = time().'.'.$cv->getClientOriginalExtension();
            $cv->move(public_path('CV'), $cvName);
            if($hero->cv){
                unlink(public_path('CV/'.$hero->cv));
            }
            $hero->cv = $cvName;}
        $hero->save();
        return response()->json([
            'success' => true,
            'data' => $hero,
            'message'=>'Hero section updated successfully'
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hero $hero)
    {
        if (Hero::first() === null) {
            return response()->json([
                'message' => 'No hero section found to delete'
            ],404);
        }
        $hero = Hero::first();
        if($hero->image){
            unlink(public_path('images/'.$hero->image));
        }
        if($hero->cv){
            unlink(public_path('CV/'.$hero->cv));
        }
        $hero->delete();
        return response()->json([
            'success' => true,
            'message' => 'Hero section deleted successfully'
        ],200);
    }
}
