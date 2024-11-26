<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Location;
use App\Models\Speciality;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    // List all doctors (public)
    public function index()
    {
        return response()->json(Doctor::with(['locations', 'specialities', 'exams'])->get());
    }

    // Show a specific doctor (public)
    public function show($id)
    {
        $doctor = Doctor::with('locations')->findOrFail($id);
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
            'photo_location' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'exists:locations,id', // Validate each ID
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to create doctors.'], 403);
        }

        if ($request->hasFile('photo_location')) {
            $image = $request->file('photo_location');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put('doctors/' . $imageName, file_get_contents($image));
            $photoLocation = 'storage/doctors/' . $imageName;
        } else {
            $photoLocation = null;
        }

        $doctor = Doctor::create([
            'name' => $request->name,
            'crm' => $request->crm,
            'phone' => $request->phone,
            'email' => $request->email,
            'photo_location' => $photoLocation,
        ]);

        // Attach locations if provided
        if ($request->has('location_ids')) {
            $doctor->locations()->sync($request->location_ids);
        }

        return response()->json($doctor->load('locations'), 201);
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
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'exists:locations,id',
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
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put('doctors/' . $imageName, file_get_contents($image));
            $doctor->photo_location = 'storage/doctors/' . $imageName;
        }

        $doctor->update($request->only(['name', 'crm', 'phone', 'email']));

        // Sync locations if provided
        if ($request->has('location_ids')) {
            $doctor->locations()->sync($request->location_ids);
        }

        return response()->json($doctor->load('locations'));
    }

    // Delete a doctor (only admin)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You do not have permission to delete doctors.'], 403);
        }

        $doctor = Doctor::findOrFail($id);
        $doctor->locations()->detach();
        $doctor->delete();

        return response()->json(null, 204);
    }

    public function getByLocation($locationId)
    {
        $location = Location::find($locationId);

        if (!$location) {
            return response()->json([
                'message' => 'Location not found'
            ], 404);
        }

        $doctors = $location->doctors()
            ->with(['specialities', 'exams'])
            ->get();

        return response()->json($doctors);
    }
    public function getByService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:speciality,exam',
            'id' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->type === 'speciality') {
                $speciality = Speciality::find($request->id);

                if (!$speciality) {
                    return response()->json([
                        'message' => 'Speciality not found'
                    ], 404);
                }

                $doctors = $speciality->doctors()
                    ->with(['locations', 'specialities', 'exams'])
                    ->get();
            } else { // exam
                $exam = Exam::find($request->id);

                if (!$exam) {
                    return response()->json([
                        'message' => 'Exam not found'
                    ], 404);
                }

                $doctors = $exam->doctors()
                    ->with(['locations', 'specialities', 'exams'])
                    ->get();
            }

            return response()->json([
                'data' => $doctors,
                'message' => 'Doctors retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
