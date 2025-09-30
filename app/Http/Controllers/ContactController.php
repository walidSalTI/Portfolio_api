<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Stevebauman\Purify\Facades\Purify;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contact = Contact::first(); 
        if (! $contact) {
            return response()->json([
                'message' => 'No contact section found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Contact::first()) {
            return response()->json([
                'message' => 'Contact section already exists'
            ], 400);
        }
        $request->validate([
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:50',
            'address'      => 'required|string|max:255',
            'display'      => 'required|boolean',
            'social_links' => 'nullable|array',
        ]);
        $portfolio = Portfolio::first();
        $contact = new Contact();
        $contact->email = $request->email;
        $contact->phone = Purify::clean($request->phone);
        $contact->address = Purify::clean($request->address);
        if ($request->has('display')) {
            $contact->display = $request->display;
        }
        if ($request->has('social_links')) {
            $contact->social_links = $request->social_links->map(function($social){
                return Purify::clean($social);
            });
        }
        if(empty($contact->phone)||empty($contact->address)){
            return response()->json([
                'message' => 'phone or address or contact  cannot be empty after sanitization'
            ], 400);
        }
        $contact->portfolio_id = $portfolio->id; 
        $contact->save();
        return response()->json([
            'success' => true,
            'data' => $contact
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //empty for now
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Contact::first() === null) {
            return response()->json([
                'message' => 'No contact section found in update'
            ], 404);
        }
        $contact = Contact::first();
        $request->validate([
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:50',
            'address'      => 'required|string|max:255',
            'display'      => 'required|boolean',
            'social_links' => 'nullable|array',
        ]);
        $contact->email = $request->email ?? $contact->email;
        $contact->phone = empty($cleaned_phone = Purify::clean($request->phone))? $contact->phone :$cleaned_phone ;
        $contact->address = empty($cleaned_address = Purify::clean($request->address))? $contact->address :$cleaned_address;
        if ($request->has('display')) {
            $contact->display = $request->display;
        }
        if ($request->has('social_links')) {
            $contact->social_links = $request->social_links->map(function($social){
                return Purify::clean($social);
            });
        }
        $contact->save();
        return response()->json([
            'success' => true,
            'message'=>'updated successfully',
            'data' => $contact
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        if (Contact::first() === null) {
            return response()->json([
                'message' => 'No contact section found to delete'
            ], 404); 
        }
        $contact = Contact::first();
        $contact->delete();
        return response()->json([
            'success' => true,
            'message' => 'Contact section deleted successfully'
        ], 200);
    }
}
