<?php

namespace App\Http\Controllers;

use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SpecialityController extends Controller
{
    public function index()
    {
        return response()->json(Speciality::all());
    }

    public function show($id)
    {
        $speciality = Speciality::find($id);
        if (!$speciality) {
            return response()->json(['message' => 'Speciality not found'], 404);
        }
        return response()->json($speciality);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'estimated_time_in_minutes' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $speciality = Speciality::create($request->all());
        return response()->json($speciality, 201);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $speciality = Speciality::find($id);
        if (!$speciality) {
            return response()->json(['message' => 'Speciality not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'estimated_time_in_minutes' => 'integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $speciality->update($request->all());
        return response()->json($speciality);
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $speciality = Speciality::find($id);
        if (!$speciality) {
            return response()->json(['message' => 'Speciality not found'], 404);
        }

        $speciality->delete();
        return response()->json(null, 204);
    }

    public function assignToDoctor(Request $request, $specialityId)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $speciality = Speciality::find($specialityId);
        if (!$speciality) {
            return response()->json(['message' => 'Speciality not found'], 404);
        }

        $speciality->doctors()->attach($request->doctor_id);
        return response()->json(['message' => 'Doctor assigned successfully']);
    }
}
