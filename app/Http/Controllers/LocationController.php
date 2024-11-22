<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class LocationController extends Controller
{

    // List all locations (public)
    public function index()
    {
        return response()->json(Location::all());
    }

    // Show a specific location (public)
    public function show($id)
    {
        $location = Location::findOrFail($id);
        return response()->json($location);
    }

    // Create a new location (only admin)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'email' => 'required|email|unique:locations,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user has 'admin' role
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to create locations.'], 403);
        }

        $location = Location::create($request->all());

        return response()->json($location, 201);
    }

    // Update a location (only admin)
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'email' => 'required|email|unique:locations,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user has 'admin' role
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to update locations.'], 403);
        }

        $location = Location::findOrFail($id);
        $location->update($request->all());

        return response()->json($location);
    }

    // Delete a location (only admin)
    public function destroy($id)
    {
        // Check if user has 'admin' role
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to delete locations.'], 403);
        }

        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Location deleted successfully']);
    }

    public function getServices($id)
    {
        $location = Location::with(['doctors.specialities', 'doctors.exams'])->findOrFail($id);

        $specialities = $location->doctors->flatMap(function ($doctor) {
            return $doctor->specialities;
        })->unique('id')->values();

        $exams = $location->doctors->flatMap(function ($doctor) {
            return $doctor->exams;
        })->unique('id')->values();

        return response()->json([
            'specialities' => $specialities,
            'exams' => $exams
        ]);
    }
}
