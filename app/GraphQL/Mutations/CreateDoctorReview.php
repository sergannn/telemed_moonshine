<?php

namespace App\GraphQL\Mutations;

use App\Models\Review;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateDoctorReview
{
    /**
     * Create a new doctor review for a patient
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @return array
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context)
    {
        // Get the authenticated user (doctor)
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            throw new \Exception('Unauthenticated');
        }

        // Get the doctor associated with the authenticated user
        $doctor = Doctor::where('user_id', $user->id)->first();
        
        if (!$doctor) {
            throw new \Exception('Doctor not found');
        }

        $input = $args['input'];
        
        // Validate that the patient exists
        $patient = Patient::find($input['patient_id']);
        if (!$patient) {
            throw new \Exception('Patient not found');
        }

        // Check if this doctor has already reviewed this patient
        $existingReview = Review::where('doctor_id', $doctor->id)
            ->where('patient_id', $input['patient_id'])
            ->first();

        if ($existingReview) {
            throw new \Exception('You have already reviewed this patient');
        }

        // Create the review
        $review = Review::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $input['patient_id'],
            'rating' => $input['rating'],
            'review' => $input['review'],
        ]);

        return [
            'review' => $review,
            'status' => 'success'
        ];
    }
}
