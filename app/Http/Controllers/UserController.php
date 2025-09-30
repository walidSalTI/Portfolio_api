<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Stevebauman\Purify\Facades\Purify;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $user = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'image' => $user->image_url,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
        return response()->json($user,200);
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
    public function show(string $id)
    {
        //empty for now 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|',
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = auth()->user();
        $user->name = empty(Purify::clean($request->name)) ? $user->name : Purify::clean($request->name);
        $user->email = $request->email ?? $user->email;
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images'),$imageName);
            $user->image = $imageName;
        }
        $user->save();
        return response()->json(
            [
            'message'=>'User updated successfully',
            'user' => $user
            ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //empty for now
    }
}
