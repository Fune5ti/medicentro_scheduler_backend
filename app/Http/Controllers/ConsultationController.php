<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\DoctorAvailability;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['doctorAvailability.doctor', 'patient'])->get();
        return response()->json($consultations);
    }

    public function show($id)
    {
        $consultation = Consultation::with(['doctorAvailability.doctor', 'patient'])->find($id);

        if (!$consultation) {
            return response()->json(['message' => 'Consultation not found'], 404);
        }

        return response()->json($consultation);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_availability_id' => 'required|exists:doctor_availabilities,id',
            'patient_id' => 'required|exists:patients,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if doctor is available
        $availability = DoctorAvailability::find($request->doctor_availability_id);
        if (!$this->isDoctorAvailable($availability, $request->start_time, $request->end_time)) {
            return response()->json(['message' => 'Doctor is not available at this time'], 422);
        }

        $consultation = Consultation::create($request->all());
        return response()->json($consultation, 201);
    }

    public function update(Request $request, $id)
    {
        $consultation = Consultation::find($id);

        if (!$consultation) {
            return response()->json(['message' => 'Consultation not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'doctor_availability_id' => 'exists:doctor_availabilities,id',
            'patient_id' => 'exists:patients,id',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'status' => 'in:scheduled,canceled,completed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has(['start_time', 'end_time', 'doctor_availability_id'])) {
            $availability = DoctorAvailability::find($request->doctor_availability_id ?? $consultation->doctor_availability_id);
            if (!$this->isDoctorAvailable($availability, $request->start_time, $request->end_time, $consultation->id)) {
                return response()->json(['message' => 'Doctor is not available at this time'], 422);
            }
        }

        $consultation->update($request->all());
        return response()->json($consultation);
    }

    public function destroy($id)
    {
        $consultation = Consultation::find($id);

        if (!$consultation) {
            return response()->json(['message' => 'Consultation not found'], 404);
        }

        $consultation->delete();
        return response()->json(null, 204);
    }

    private function isDoctorAvailable($availability, $startTime, $endTime, $excludeConsultationId = null)
    {
        if (!$availability) {
            return false;
        }

        // Check if requested time is within doctor's availability
        if ($startTime < $availability->start_time || $endTime > $availability->end_time) {
            return false;
        }

        // Check for overlapping consultations
        $query = Consultation::where('doctor_availability_id', $availability->id)
            ->where('status', '!=', 'canceled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            });

        if ($excludeConsultationId) {
            $query->where('id', '!=', $excludeConsultationId);
        }

        return !$query->exists();
    }
}
