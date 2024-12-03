<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\DoctorAvailability;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'patient_id' => 'required_without:patient_data|exists:patients,id',
            'patient_data' => 'required_without:patient_id|array',
            'patient_data.full_name' => 'required_with:patient_data|string|max:255',
            'patient_data.email' => 'required_with:patient_data|email|unique:patients,email',
            'patient_data.nif' => 'required_with:patient_data|string|unique:patients,nif',
            'patient_data.phone' => 'required_with:patient_data|string',
            'patient_data.birth_date' => 'required_with:patient_data|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // Check if doctor availability exists and has space
                $availability = DoctorAvailability::findOrFail($request->doctor_availability_id);

                // Check if the requested time slot falls within the availability period
                if (!$this->isDoctorAvailable($availability, $request->start_time, $request->end_time)) {
                    return response()->json(['message' => 'Doctor is not available at this time'], 422);
                }

                // Check max_per_day limit
                $consultationsCount = Consultation::where('doctor_availability_id', $availability->id)
                    ->whereDate('created_at', Carbon::today())
                    ->where('status', '!=', 'canceled')
                    ->count();

                if ($consultationsCount >= $availability->max_per_day) {
                    return response()->json([
                        'message' => 'Maximum consultations per day limit reached for this availability'
                    ], 422);
                }

                // Handle patient creation if patient_data is provided
                $patientId = $request->patient_id;
                if (!$patientId && $request->has('patient_data')) {
                    $patient = Patient::create($request->patient_data);
                    $patientId = $patient->id;
                }

                // Create the consultation
                $consultation = Consultation::create([
                    'doctor_availability_id' => $request->doctor_availability_id,
                    'patient_id' => $patientId,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'user_id' => Auth::user()->id,
                    'status' => 'scheduled'
                ]);

                $consultation->load(['doctorAvailability.doctor', 'patient', 'user']);

                return response()->json($consultation, 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating consultation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function isDoctorAvailable($availability, $startTime, $endTime)
    {
        if (!$availability) {
            return false;
        }

        // Convert times to Carbon instances for comparison
        $availabilityStart = Carbon::parse($availability->available_date . ' ' . $availability->start_time);
        $availabilityEnd = Carbon::parse($availability->available_date . ' ' . $availability->end_time);
        $requestedStart = Carbon::parse($availability->available_date . ' ' . $startTime);
        $requestedEnd = Carbon::parse($availability->available_date . ' ' . $endTime);

        // Check if requested time is within doctor's availability
        if ($requestedStart < $availabilityStart || $requestedEnd > $availabilityEnd) {
            return false;
        }

        // Check for overlapping consultations
        $overlappingConsultations = Consultation::where('doctor_availability_id', $availability->id)
            ->where('status', '!=', 'canceled')
            ->where(function ($query) use ($startTime, $endTime, $availability) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->exists();

        return !$overlappingConsultations;
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
}
