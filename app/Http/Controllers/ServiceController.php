<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Stevebauman\Purify\Facades\Purify;
class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();

        if ($services->isEmpty()) {
            return response()->json([
                'message' => 'No services found'
            ], 404);
        }

        $services = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'icon' => $service->icon_url, 
                'display' => $service->display,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $services,
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
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico',
            'display' => 'nullable|boolean',
        ]);
        $portfolio = Portfolio::firstOrFail();
        $service = new Service();
        $service->title = Purify::clean($request->title);
        $service->description = Purify::clean($request->description);
        if(empty($service->title)||empty($service->description)){
            return response()->json([
                'message' => 'title or description cannot be empty after sanitization'
            ], 400);
        }
        if ($request->has('display')) {
            $service->display = $request->display;
        }

        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconName = time() . '.' . $icon->getClientOriginalExtension();
            $icon->move(public_path('images/services/'), $iconName);
            $service->icon = $iconName;
        }
        $service->portfolio_id = $portfolio->id;
        $service->save();

        return response()->json([
            'success' => true,
            'data' => $service
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $service_id)
    {
        $service = Service::where('id', $service_id)->first();

        if (!$service) {
            return response()->json([
                'message' => 'There is no such service'
            ], 404);
        }

        $service = [
            'id' => $service->id,
            'title' => $service->title,
            'description' => $service->description,
            'icon' => $service->icon_url,
            'display' => $service->display,
            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $service
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $service_id)
    {
        $service = Service::where('id', $service_id)->first();

        if (!$service) {
            return response()->json([
                'message' => 'There is no such service to update'
            ], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico',
            'display' => 'nullable|boolean',
        ]);
        $service->title = empty($cleaned_title = Purify::clean($request->title))? $service->title :$cleaned_title  ;
        $service->description = empty($cleaned_description = Purify::clean($request->description))? $service->description :$cleaned_description;

        if ($request->has('display')) {
            $service->display = $request->display;
        }

        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconName = time() . '.' . $icon->getClientOriginalExtension();
            $icon->move(public_path('images/services/'), $iconName);

            if ($service->icon) {
                unlink(public_path('images/services/' . $service->icon));
            }

            $service->icon = $iconName;
        }

        $service->save();

        return response()->json([
            'success' => true,
            'data' => $service,
            'message' => 'Service updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $service_id)
    {
        $service = Service::where('id', $service_id)->first();

        if (!$service) {
            return response()->json([
                'message' => 'There is no such service to delete'
            ], 404);
        }

        if ($service->icon) {
            unlink(public_path('images/services/' . $service->icon));
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ], 200);
    }
}
