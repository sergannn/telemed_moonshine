<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            // Reviews for doctor with ID 1 (Alex 2 Vert)
            [
                'patient_id' => 1,
                'doctor_id' => 1,
                'review' => 'Очень грамотный специалист! Объяснил все доступно, назначил эффективное лечение. Консультация прошла на высшем уровне.',
                'rating' => 5,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'patient_id' => 2,
                'doctor_id' => 1,
                'review' => 'Доктор внимательно выслушал все жалобы, провел тщательный осмотр. Очень доволен результатом лечения.',
                'rating' => 4,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'patient_id' => 3,
                'doctor_id' => 1,
                'review' => 'Профессионал своего дела! Рекомендую всем знакомым. Лечение помогло быстро избавиться от проблемы.',
                'rating' => 5,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],

            // Reviews for other doctors
            [
                'patient_id' => 4,
                'doctor_id' => 2,
                'review' => 'Хороший врач, но немного торопился во время приема. В целом лечение эффективное.',
                'rating' => 4,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'patient_id' => 5,
                'doctor_id' => 3,
                'review' => 'Отличный специалист! Очень подробно все объяснил, ответил на все вопросы. Чувствую значительное улучшение.',
                'rating' => 5,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'patient_id' => 6,
                'doctor_id' => 4,
                'review' => 'Доктор вежливый и внимательный. Назначенное лечение помогло справиться с проблемой.',
                'rating' => 4,
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'patient_id' => 7,
                'doctor_id' => 5,
                'review' => 'Профессиональный подход к лечению. Все процедуры были объяснены доступным языком. Рекомендую!',
                'rating' => 5,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
        ];

        DB::table('reviews')->insert($reviews);
    }
}
