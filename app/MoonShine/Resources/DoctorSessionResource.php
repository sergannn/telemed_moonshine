<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\DoctorSession;

use MoonShine\Laravel\Resources\ModelResource;
use App\MoonShine\Resources\WeekDayResource;
use MoonShine\Decorations\Block;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @extends ModelResource<DoctorSession>
 */
class DoctorSessionResource extends ModelResource
{
    protected string $model = DoctorSession::class;

    protected string $title = 'Doctor Sessions';

    protected string $column = 'title';

    protected bool $createInModal = true;
  
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexFields(): array {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Doctor', 'doctor', 'user.first_name', resource:  DoctorResource::class)
                ->nullable(),
            Text::make('Session Meeting Time', 'session_meeting_time'),
            Text::make('Session Gap', 'session_gap'),
        ];
    }

    public function formFields(): array {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Doctor', 'doctor', 'user.first_name', resource: DoctorResource::class)
                ->nullable(),
            Select::make('Session Meeting Time:', 'session_meeting_time')
                ->options(DoctorSession::SESSION_MEETING_TIME)
                ->required(),
            Select::make('Session Gap', 'session_gap')
                ->options(DoctorSession::GAPS)
                ->required(),
            HasMany::make('Week Days', 'sessionWeekDays', resource:  WeekDayResource::class)
                ->fields([
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
                ])
               ->creatable()
             //   ->removable(),
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
            HasMany::make('Week Days', 'sessionWeekDays', resource: WeekDayResource::class)
                ->fields([
                    Text::make('Day of Week', 'day_of_week'),
                    Text::make('Start Time', 'start_time'),
                    Text::make('End Time', 'end_time'),
                    Text::make('Start Time Type', 'start_time_type'),
                    Text::make('End Time Type', 'end_time_type'),
                ]),
        ];
    }

    /**
     * @param DoctorSession $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(mixed $item): array
    {
        return [
            'doctor_id' => 'required|unique:doctor_sessions,doctor_id,' . $item->id,
            'session_meeting_time' => 'required',
            'session_gap' => 'required',
        ];
    }
}
