<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\UI\Fields\ID;

//use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Select;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\Laravel\Fields\Relationships\BelongsTo; 
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\UI\Components\MoonShineComponent;
//use use MoonShine\UI\Fields\Image;
use VI\MoonShineSpatieMediaLibrary\Fields\MediaLibrary;

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
         
            Text::make('Name', 'user.first_name'),
            Text::make('Last name', 'user.last_name'),
            Text::make('Описание_', 'user.description'),
          /*  Image::make('Image')
            ->disk('public')
           // ->dir('doctors')
            ->changePreview(fn($item) => $item->getFirstMediaUrl('profile'))
            ->hideOnIndex(), // or keep if you want thumbnails in index
            */
            MediaLibrary::make('Cover', 'profile'),
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
                    MediaLibrary::make('Cover', 'profile')->multiple(),
                  /*  Image::make('Image')
                        ->disk('public')
                        ->dir('doctors')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'gif'])
                        ->onApply(function(Doctor $item, $value) {
                            if ($value) {
                                $item->clearMediaCollection('profile');
                                $item->addMedia($value)->toMediaCollection('profile');
                            }
                            return $item;
                        }),*/
                HasMany::make('Sessions', 'doctorSession', resource:  DoctorSessionResource::class)
                    ->fields([
                        Select::make('Session Meeting Time', 'session_meeting_time')
                            ->options(\App\Models\DoctorSession::SESSION_MEETING_TIME)
                            ->required(),
                        Select::make('Session Gap', 'session_gap')
                            ->options(\App\Models\DoctorSession::GAPS)
                            ->required(),
                    HasMany::make('Week Days', 'session_week_days', resource:  WeekDayResource::class)
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
            HasMany::make('Sessions', 'doctorSession', resource: DoctorSessionResource::class)
                ->fields([
                    Text::make('Session Meeting Time', 'session_meeting_time'),
                    Text::make('Session Gap', 'session_gap'),
                    HasMany::make('Week Days', 'sessionWeekDays', resource:  WeekDayResource::class)
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

    
}
