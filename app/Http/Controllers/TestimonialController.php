<?php

namespace App\Http\Controllers;

use App\Models\Testimonials;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Stevebauman\Purify\Facades\Purify;
class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = Testimonials::all();
        if ($testimonials->isEmpty()) {
            return response()->json([
                'message' => 'No testimonials found'
            ], 404);
        }
        $testimonials = $testimonials->map(function($testimonial) {
            return [
                'id' => $testimonial->id,
                'client_name' => $testimonial->client_name,
                'client_position' => $testimonial->client_position,
                'qoute' => $testimonial->qoute,
                'client_image' => $testimonial->client_image_url,
                'display' => $testimonial->display,
                'portfolio_id' => $testimonial->portfolio_id,
                'created_at' => $testimonial->created_at,
                'updated_at' => $testimonial->updated_at,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $testimonials,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'qoute' => 'required|string',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico',
            'client_position' => 'nullable|string|max:255',
            'display' => 'nullable|boolean',
        ]);
        $portfolio = Portfolio::firstOrFail();
        $testimonial = new Testimonials();
        $testimonial->client_name = Purify::clean($request->client_name);
        $testimonial->qoute = Purify::clean($request->qoute) ;
        $testimonial->client_position = Purify::clean($request->client_position) ?? null;
        if(empty($testimonial->client_name)||empty($testimonial->qoute)){
            return response()->json([
                'message' => 'client_name or qoute cannot be empty after sanitization'
            ], 400);
        }
        $testimonial->portfolio_id = $request->portfolio_id;
        if($request->has('display')){
            $testimonial->display = $request->display;
        }
        if ($request->hasFile('client_image')) {
            $image = $request->file('client_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/testimonials/'), $imageName);
            $testimonial->client_image = $imageName;
        }
        $testimonial->portfolio_id = $portfolio->id;
        $testimonial->save();

        return response()->json([
            'success' => true,
            'data' => $testimonial
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $testimonial_id)
    {
        $testimonial = Testimonials::where('id', $testimonial_id)->first();
        if (!$testimonial) {
            return response()->json([
                'message' => 'There is no such testimonial'
            ], 404);
        }
        $testimonial = [
            'id' => $testimonial->id,
            'client_name' => $testimonial->client_name,
            'client_position' => $testimonial->client_position,
            'qoute' => $testimonial->qoute,
            'client_image' => $testimonial->client_image_url,
            'display' => $testimonial->display,
            'portfolio_id' => $testimonial->portfolio_id,
            'created_at' => $testimonial->created_at,
            'updated_at' => $testimonial->updated_at,
        ];
        return response()->json([
            'success' => true,
            'data' => $testimonial
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , string $testimonial_id)
    {
        $testimonial = Testimonials::where('id', $testimonial_id)->first();
        if (!$testimonial) {
            return response()->json([
                'message' => 'There is no such testimonial to update'
            ], 404);
        }
        $request->validate([
            'client_name' => 'required|string|max:255',
            'qoute' => 'required|string',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'client_position' => 'nullable|string|max:255',
            'display' => 'nullable|boolean',
        ]);

        $testimonial->client_name = empty($cleaned_name = Purify::clean($request->client_name)) ? $testimonial->client_name : $cleaned_name;
        $testimonial->quote = empty($cleaned_quote = Purify::clean($request->quote)) ? $testimonial->quote : $cleaned_quote;
        $testimonial->client_position = empty($cleaned_position = Purify::clean($request->client_position)) ? $testimonial->client_position : $cleaned_position;
        $testimonial->portfolio_id = $request->portfolio_id ?? $testimonial->portfolio_id;
        if ($request->has('display')) {
            $testimonial->display = $request->display;
        }
        if ($request->hasFile('client_image')) {
            $image = $request->file('client_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/testimonials/'), $imageName);
            if ($testimonial->client_image) {
                unlink(public_path('images/testimonials/' . $testimonial->client_image));
            }
            $testimonial->client_image = $imageName;
        }
        $testimonial->save();
        return response()->json([
            'success' => true,
            'data' => $testimonial,
            'message' => 'Testimonial updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $testimonial_id)
    {
        $testimonial = Testimonials::where('id', $testimonial_id)->first();
        if (!$testimonial) {
            return response()->json([
                'message' => 'There is no such testimonial to delete'
            ], 404); 
        }
        if ($testimonial->client_image) {
            unlink(public_path('images/testimonials/' . $testimonial->client_image));
        }
        $testimonial->delete();
        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully'
        ], 200);
    }
}
