<?php

namespace App\GraphQL\Mutations;

use App\Models\Review;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateReview
{
    public function __invoke($rootValue, array $args, GraphQLContext $context)
    {
        // Get the authenticated user
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            throw new \Exception('Необходима авторизация для создания отзыва');
        }

        // Find the patient associated with the user
        $patient = Patient::where('user_id', $user->id)->first();
        
        if (!$patient) {
            throw new \Exception('Пользователь не является пациентом');
        }

        $input = $args['input'];

        // Verify that the doctor exists
        $doctor = Doctor::find($input['doctor_id']);
        if (!$doctor) {
            throw new \Exception('Доктор не найден');
        }

        // Create the review with proper patient_id (from patient table) and doctor_id
        $review = Review::create([
            'patient_id' => $patient->id, // patient.id from patients table
            'doctor_id' => $input['doctor_id'], // doctor.id from doctors table
            'rating' => $input['rating'],
            'review' => $input['review'],
        ]);

        return [
            'review' => $review,
            'status' => 'success'
        ];
    }
}
