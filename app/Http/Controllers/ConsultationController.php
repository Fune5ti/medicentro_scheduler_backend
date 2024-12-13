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
use Illuminate\Support\Facades\Storage;

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
        $patientData = json_decode($request->patient_data, true) ;

        $request->merge(['patient_data' => $patientData]);

        $validator = Validator::make($request->all(), [
            'doctor_availability_id' => 'required|exists:doctor_availabilities,id',
            'patient_id' => 'required_without:patient_data|nullable|exists:patients,id',
            'patient_data' => 'required_without:patient_id|nullable|array',
            'patient_data.full_name' => 'required_with:patient_data|string|max:255',
            'patient_data.email' => 'required_with:patient_data|email',
            'patient_data.nif' => 'required_with:patient_data|string',
            'patient_data.phone' => 'required_with:patient_data|string',
            'patient_data.birth_date' => 'required_with:patient_data|date',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request,$validator) {
                // Check if doctor availability exists and has space
                $availability = DoctorAvailability::findOrFail($request->doctor_availability_id);


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
                $validatedData = $validator->validated();
                $patientData = $validatedData['patient_data'];
                if (!$patientId && $request->has('patient_data')) {
                    $existingPatient = Patient::where('email', $patientData['email'])->first();
    
                    if ($existingPatient) {
                        $patientId = $existingPatient->id;
                    } else {
                        $patient = Patient::create($patientData);
                        $patientId = $patient->id;
                    }
                }

                $consultationData = [
                    'doctor_availability_id' => $request->doctor_availability_id,
                    'patient_id' => $patientId,
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'user_id' => Auth::user()->id,
                    'status' => 'scheduled',
                    'notes' => $request->notes
                ];

                // Handle file upload if present
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('consultations', $fileName, 'public');

                    $consultationData['file_path'] = $filePath;
                    $consultationData['file_name'] = $file->getClientOriginalName();
                }

                $consultation = Consultation::create($consultationData);
                $consultation->load(['doctorAvailability.doctor', 'patient']);

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
            'status' => 'in:scheduled,canceled,completed',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle file upload if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($consultation->file_path) {
                Storage::disk('public')->delete($consultation->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('consultations', $fileName, 'public');

            $consultation->file_path = $filePath;
            $consultation->file_name = $file->getClientOriginalName();
        }

        $consultation->update($request->except('file'));
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
