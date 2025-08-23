<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\WeekDay;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @extends ModelResource<WeekDay>
 */
class WeekDayResource extends ModelResource
{
    protected string $model = WeekDay::class;

    protected string $title = 'Week Days';

    protected string $column = 'title';

    protected bool $createInModal = true;
  
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexFields(): array {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Doctor Session', 'doctorSession', 'id', resource: DoctorSessionResource::class)
                ->nullable(),
            Text::make('Day of Week', 'day_of_week'),
            Text::make('Start Time', 'start_time'),
            Text::make('End Time', 'end_time'),
            Text::make('Start Time Type', 'start_time_type'),
            Text::make('End Time Type', 'end_time_type'),
        ];
    }

    public function formFields(): array {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Doctor Session', 'doctorSession', 'id', resource: DoctorSessionResource::class)
                ->nullable(),
            Select::make('Day of Week', 'day_of_week')
                ->options([
                    'Monday' => 'Monday',
                    'Tuesday' => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday' => 'Thursday',
                    'Friday' => 'Friday',
                    'Saturday' => 'Saturday',
                    'Sunday' => 'Sunday',
                ])
                ->required(),
            Text::make('Start Time', 'start_time')
                ->required(),
            Text::make('End Time', 'end_time')
                ->required(),
            Select::make('Start Time Type', 'start_time_type')
                ->options([
                    'AM' => 'AM',
                    'PM' => 'PM',
                ])
                ->required(),
            Select::make('End Time Type', 'end_time_type')
                ->options([
                    'AM' => 'AM',
                    'PM' => 'PM',
                ])
                ->required(),
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
        ];
    }

    /**
     * @param WeekDay $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(mixed $item): array
    {
        return [
            'doctor_session_id' => 'required',
            'day_of_week' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_time_type' => 'required',
            'end_time_type' => 'required',
        ];
    }
}
