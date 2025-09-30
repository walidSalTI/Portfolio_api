<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portfolio = Portfolio::with([
            'hero',
            'about',
            'skills',
            'achievements',
            'projects',
            'testimonials',
            'services',
            'contact'
        ])->first();
        if (! isset($portfolio)) {
            return response()->json([
                'message' => 'No portfolio found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $portfolio
        ],200);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //empty for now
    }

    /**
     * Display the specified resource.
     */
    public function show(Portfolio $portfolio)
    {
        //empty for now
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $portfolio = Portfolio::first();
        if (! isset($portfolio)) {
            return response()->json([
            'message' => 'No portfolio found'
            ], 404);
        }
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
        ]);
        $portfolio->title = empty($cleaned_title = Purify::clean($request->title))? $portfolio->title :$cleaned_title  ;
        $portfolio->description = empty($cleaned_description = Purify::clean($request->description))? $portfolio->description :$cleaned_description;
        $portfolio->footer_text = empty($cleaned_footer_text = Purify::clean($request->footer_text))? $portfolio->footer_text :$cleaned_footer_text;
        
        $portfolio->user_id = auth()->user()->id;
        $portfolio->save();
        return response()->json([
            'success' => true,
            'data' => $portfolio
        ],200);
    
        }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio)
    {
        //empty for now
    }
}
