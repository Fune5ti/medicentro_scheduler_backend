<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorAvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = DoctorAvailability::with(['doctor', 'serviceable'])->get();
        return response()->json($availabilities);
    }

    public function show($id)
    {
        $availability = DoctorAvailability::with(['doctor', 'serviceable'])->find($id);

        if (!$availability) {
            return response()->json(['message' => 'Availability not found'], 404);
        }

        return response()->json($availability);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'available_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'serviceable_type' => 'required|in:App\Models\Exam,App\Models\Speciality',
            'serviceable_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if doctor is already scheduled for this time
        if ($this->hasOverlappingAvailability($request)) {
            return response()->json(['message' => 'Doctor already has availability during this time'], 422);
        }

        $availability = DoctorAvailability::create($request->all());
        return response()->json($availability->load(['doctor', 'serviceable']), 201);
    }

    public function update(Request $request, $id)
    {
        $availability = DoctorAvailability::find($id);

        if (!$availability) {
            return response()->json(['message' => 'Availability not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'exists:doctors,id',
            'available_date' => 'date|after_or_equal:today',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'serviceable_type' => 'in:App\Models\Exam,App\Models\Speciality',
            'serviceable_id' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($this->hasOverlappingAvailability($request, $id)) {
            return response()->json(['message' => 'Doctor already has availability during this time'], 422);
        }

        $availability->update($request->all());
        return response()->json($availability->load(['doctor', 'serviceable']));
    }

    public function destroy($id)
    {
        $availability = DoctorAvailability::find($id);

        if (!$availability) {
            return response()->json(['message' => 'Availability not found'], 404);
        }

        $availability->delete();
        return response()->json(null, 204);
    }

    private function hasOverlappingAvailability(Request $request, $excludeId = null)
    {
        $query = DoctorAvailability::where('doctor_id', $request->doctor_id)
            ->where('available_date', $request->available_date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getByDoctor($doctorId)
    {
        $doctor = Doctor::find($doctorId);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $availabilities = $doctor->availabilities()
            ->with('serviceable')
            ->get();

        return response()->json($availabilities);
    }
}
