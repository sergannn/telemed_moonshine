<?php

namespace App\GraphQL\Mutations;

use App\Models\Appointment;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateAppointmentMutation 
{
    protected $attributes = [
        'name' => 'createAppointment',
        'description' => 'Creates a new appointment'
    ];

    public function type(): Type
    {
        return \GraphQL::type('Appointment');
    }

    public function args(): array
    {
        return [
            'doctor_id' => [
                'name' => 'doctor_id',
                'type' => Type::nonNull(Type::string()),
            ],
            'patient_id' => [
                'name' => 'patient_id',
                'type' => Type::nonNull(Type::string()),
            ],
            'date' => [
                'name' => 'date',
                'type' => Type::nonNull(Type::string()),
            ],
            'from_time' => [
                'name' => 'from_time',
                'type' => Type::nonNull(Type::string()),
            ],
            'from_time_type' => [
                'name' => 'from_time_type',
                'type' => Type::nonNull(Type::string()),
            ],
            'to_time' => [
                'name' => 'to_time',
                'type' => Type::nonNull(Type::string()),
            ],
            'to_time_type' => [
                'name' => 'to_time_type',
                'type' => Type::nonNull(Type::string()),
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::string()),
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::nonNull(Type::string()),
            ],
            'service_id' => [
                'name' => 'service_id',
                'type' => Type::nonNull(Type::string()),
            ],
            'payment_type' => [
                'name' => 'payment_type',
                'type' => Type::nonNull(Type::string()),
            ],
            'payable_amount' => [
                'name' => 'payable_amount',
                'type' => Type::nonNull(Type::string()),
            ],
            'appointment_unique_id' => [
                'name' => 'appointment_unique_id',
                'type' => Type::string(),
            ],
        ];
    }

    public function __invoke(mixed $_, array $args)
    {
        // Check if an appointment already exists for the same doctor, date, and time
        $existingAppointment = Appointment::where('doctor_id', $args['doctor_id'])
            ->where('date', $args['date'])
            ->where('from_time', $args['from_time'])
            ->where('from_time_type', $args['from_time_type'])
            ->where('to_time', $args['to_time'])
            ->where('to_time_type', $args['to_time_type'])
            ->whereIn('status', [Appointment::BOOKED, Appointment::CHECK_IN, Appointment::CHECK_OUT])
            ->first();

        if ($existingAppointment) {
            throw new \Exception('An appointment already exists for this doctor at the specified date and time.');
        }

        // If no duplicate, create the new appointment
        $appointmentData = [
            'doctor_id' => $args['doctor_id'],
            'patient_id' => $args['patient_id'],
            'date' => $args['date'],
            'from_time' => $args['from_time'],
            'from_time_type' => $args['from_time_type'],
            'to_time' => $args['to_time'],
            'to_time_type' => $args['to_time_type'],
            'status' => $args['status'],
            'description' => $args['description'],
            'service_id' => $args['service_id'],
            'payment_type' => $args['payment_type'],
            'payable_amount' => $args['payable_amount'],
            'appointment_unique_id' => $args['appointment_unique_id'] ?? strtoupper(Appointment::generateAppointmentUniqueId($args)),
        ];

        return Appointment::create($appointmentData);
    }
}
