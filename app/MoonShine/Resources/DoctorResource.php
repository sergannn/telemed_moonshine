<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;

use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;

use MoonShine\Fields\Image;
use MoonShine\Fields\Field;
use MoonShine\Fields\Text;
use MoonShine\Fields\Select;
use MoonShine\Fields\TinyMce;
use MoonShine\Fields\Relationships\BelongsTo; 
use MoonShine\Fields\Relationships\HasMany; 
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Doctor>
 */
class DoctorResource extends ModelResource
{
    protected string $model = Doctor::class;

    protected string $title = 'Doctors';

    protected string $column = 'title';

  //  protected string $sortColumn = 'sorting';

    protected bool $createInModal = true;
  
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexFields(): array {
        return [
            ID::make()->sortable(),
         
            Text::make(
                'Name',
                'user.first_name',),
                Text::make(
                    'Описание',
                    'user.description',),
                 // Доступ к связанному полю через точку
     //           fn($item) => $item->user->first_name . ' ' . $item->user->last_name // Форматирование
        
            //    ->updateOnPreview(),
      /*      Text::make( 'Description' )
                ->updateOnPreview(),
            Image::make( 'Image' ),
          //      ->updateOnPreview(),
            TinyMce::make( 'Html' ),
            BelongsTo::make( 'Category', 'category', 'title', resource: new CategoryResource() )
                ->nullable()
                ->badge(),*/
        ];
    }

    public function formFields(): array {
        return [
          //  Box::make( [
                ID::make()->sortable(),
           
                Text::make('First name', 'user.first_name',)
            //    ->fill(fn(Doctor $item) => $item->user->first_name)
                ->onApply(function(Doctor $item, $value) {
                    $item->user()->update(['first_name' => $value]);
                    return $item;
                }),
                Text::make('Описание', 'user.description',)
                //    ->fill(fn(Doctor $item) => $item->user->first_name)
                    ->onApply(function(Doctor $item, $value) {
                        $item->user()->update(['description' => $value]);
                        return $item;
                    }),
                HasMany::make('Sessions', 'doctorSession', resource: new DoctorSessionResource())
                    ->fields([
                        Select::make('Session Meeting Time', 'session_meeting_time')
                            ->options(\App\Models\DoctorSession::SESSION_MEETING_TIME)
                            ->required(),
                        Select::make('Session Gap', 'session_gap')
                            ->options(\App\Models\DoctorSession::GAPS)
                            ->required(),
                    HasMany::make('Week Days', 'session_week_days', resource: new WeekDayResource())
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
                            ->creatable(),
                    ])
                    ->creatable(),
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
            \HasMany::make('Sessions', 'doctorSession', resource: new DoctorSessionResource())
                ->fields([
                    Text::make('Session Meeting Time', 'session_meeting_time'),
                    Text::make('Session Gap', 'session_gap'),
                    HasMany::make('Week Days', 'sessionWeekDays', resource: new WeekDayResource())
                        ->fields([
                            Text::make('Day of Week', 'day_of_week'),
                            Text::make('Start Time', 'start_time'),
                            Text::make('End Time', 'end_time'),
                            Text::make('Start Time Type', 'start_time_type'),
                            Text::make('End Time Type', 'end_time_type'),
                        ]),
                ]),
        ];
    }

    /**
     * @param Doctor $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
