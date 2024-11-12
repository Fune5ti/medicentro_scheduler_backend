<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{

    // List all doctors (public)
    public function index()
    {
        return response()->json(Doctor::all());
    }

    // Show a specific doctor (public)
    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        return response()->json($doctor);
    }

    // Create a new doctor (only admin)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'crm' => 'required|string|max:255|unique:doctors,crm',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email',
            'photo_location' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image upload
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user has 'admin' role
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to create doctors.'], 403);
        }

        // Handle the photo upload if present
        if ($request->hasFile('photo_location')) {
            $image = $request->file('photo_location');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // Generate a unique name
            Storage::disk('public')->put('doctors/' . $imageName, file_get_contents($image));
            // Set the photo location to the stored file path
            $photoLocation = 'storage/doctors/' . $imageName;
        } else {
            $photoLocation = null; // If no photo is uploaded, set as null
        }

        // Create the doctor with the validated data and photo location
        $doctor = Doctor::create([
            'name' => $request->name,
            'crm' => $request->crm,
            'phone' => $request->phone,
            'email' => $request->email,
            'photo_location' => $photoLocation, // Store the file path
        ]);

        return response()->json($doctor, 201);
    }


    // Update a doctor (only admin)
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'crm' => 'nullable|string|max:255|unique:doctors,crm,' . $id,
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:doctors,email,' . $id,
            'photo_location' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to update doctors.'], 403);
        }

        $doctor = Doctor::findOrFail($id);

        if ($request->hasFile('photo_location')) {
            $image = $request->file('photo_location');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // Generate a unique name
            Storage::disk('public')->put('doctors/' . $imageName, file_get_contents($image));
            $doctor->photo_location = 'storage/doctors/' . $imageName;
        }

        $doctor->update($request->only([
            'name',
            'crm',
            'phone',
            'email',
            'photo_location'
        ]));

        return response()->json($doctor);
    }


    // Delete a doctor (only admin)
    public function destroy($id)
    {
        // Check if user has 'admin' role
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to create doctors.'], 403);
        }

        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        return response()->json(null, 204);
    }
}
