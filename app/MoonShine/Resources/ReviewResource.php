<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;

/**
 * @extends ModelResource<Review>
 */
class ReviewResource extends ModelResource
{
    protected string $model = Review::class;

    protected string $title = 'Reviews';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Доктор', 'doctor.user.full_name'),
            Text::make('Пациент', 'patient.patientUser.full_name'),
            Text::make('Рейтинг', 'rating'),
            Text::make('Отзыв', 'review'),//->limit(50),
            Text::make('Дата создания', 'created_at')->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                BelongsTo::make('Доктор', 'doctor', 'user.full_name', resource: DoctorResource::class),
                BelongsTo::make('Пациент', 'patient', 'patientUser.full_name', resource: PatientResource::class),
                Text::make('Рейтинг', 'rating')
                    ->required(),
                    //->rules(['required', 'integer', 'min:1', 'max:5']),
                Textarea::make('Отзыв', 'review')
                    ->required(),
                    //->rules(['required', 'max:121']),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Доктор', 'doctor.user.full_name'),
            Text::make('Пациент', 'patient.patientUser.full_name'),
            Text::make('Рейтинг', 'rating'),
            Textarea::make('Отзыв', 'review'),
            Text::make('Дата создания', 'created_at'),
            Text::make('Дата обновления', 'updated_at'),
        ];
    }

    /**
     * @param Review $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
