<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;

use MoonShine\Laravel\Fields\Relationships\BelongsToMany;

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
            Text::make('Полное имя', 'user.full_name'),
         //   Text::make('Имя', 'user.first_name'),
         //   Text::make('Фамилия', 'user.last_name'),
         //   Text::make('Описание', 'user.description'),
            Text::make(
                'E-mail',
                'user.email',),
                Text::make('Время создания'    , 'user.created_at',),//->sortable(),
                Text::make('Время обновления'    , 'user.updated_at',)->sortable(),
            Text::make('Опыт работы (лет)', 'experience'),
            Text::make('Отзывы', '', function(Doctor $doctor) {
                return $doctor->reviews()->count() . ' отзывов';
            }),
          /*  Image::make('Image')
            ->disk('public')
           // ->dir('doctors')
            ->changePreview(fn($item) => $item->getFirstMediaUrl('profile'))
            ->hideOnIndex(), // or keep if you want thumbnails in index
            */
            MediaLibrary::make('Фото', 'profile'),
            Text::make('Дни работы', '', function(Doctor $doctor) {
                $days = [];
                foreach ($doctor->doctorSession as $session) {
                    foreach ($session->sessionWeekDays as $day) {
                        $days[] = $day->day_of_week;
                    }
                }
                return implode(', ', array_unique($days));
            }),
            HasMany::make('Специализации', 'specializations','name')  
//    ->multiple()
 //   ->options(\App\Models\Specialization::all()->pluck('name', 'id')),
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
           
                Text::make('First name', 'user.first_name',) ->onApply(function(Doctor $item, $value) {
                    $item->user()->update(['first_name' => $value]);
                    return $item;
                }),
                Text::make( 'Last name', 'user.last_name',)
            //    ->fill(fn(Doctor $item) => $item->user->first_name)
                ->onApply(function(Doctor $item, $value) {
                    $item->user()->update(['last_name' => $value]);
                    return $item;
                }),
                Text::make('Описание', 'user.description',)
                //    ->fill(fn(Doctor $item) => $item->user->first_name)
                    ->onApply(function(Doctor $item, $value) {
                        $item->user()->update(['description' => $value]);
                        return $item;
                    }),
                    MediaLibrary::make('Фото', 'profile')->multiple(),
                    BelongsToMany::make(__('Специальность'), 'specializations','name'),
                    Text::make('Опыт работы (лет)', 'experience'),
            /*        HasMany::make('Расписание', 'doctorSession', resource: DoctorSessionResource::class)
                        ->fields([
                            Select::make('Тип сессии', 'session_meeting_time')
                                ->options(\App\Models\DoctorSession::SESSION_MEETING_TIME)
                                ->required(),
                            Select::make('Интервал', 'session_gap')
                                ->options(\App\Models\DoctorSession::GAPS)
                                ->required(),
                            BelongsToMany::make('Дни недели', 'sessionWeekDays', 'day_of_week', resource: WeekDayResource::class)
                                ->fields([
                                    Text::make('Начало', 'start_time')->required(),
                                    Text::make('Конец', 'end_time')->required(),
                                    Select::make('Тип времени (начало)', 'start_time_type')
                                        ->options(['AM' => 'AM', 'PM' => 'PM'])
                                        ->required(),
                                    Select::make('Тип времени (конец)', 'end_time_type')
                                        ->options(['AM' => 'AM', 'PM' => 'PM'])
                                        ->required(),
                                ])
                                ->selectMode()
                                ->creatable()
                        ])
                        ->creatable(),
                        */
//    ->options(\App\Models\Specialization::all()->pluck('name', 'id')),
   // ->options(\App\Models\Specialization::all()->pluck('name', 'id')),
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
                Text::make('Дни работы', '', function(Doctor $doctor) {
                    $days = [];
                    foreach ($doctor->doctorSession as $session) {
                        foreach ($session->sessionWeekDays as $day) {
                            $days[] = $day->day_of_week;
                        }
                    }
                    return implode(', ', array_unique($days));
                })->readonly(),
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
         //   HasMany::make('Специализации', 'specializations', resource: SpecializationResource::class),
            Text::make('Дни работы', '', function(Doctor $doctor) {
                $days = [];
                foreach ($doctor->doctorSession as $session) {
                    foreach ($session->sessionWeekDays as $day) {
                        $days[] = $day->day_of_week;
                    }
                }
                return implode(', ', array_unique($days));
            }),
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
                HasMany::make('Сеансы', 'appointments', resource: UpcomingAppointmentsResource::class)
                ->fields([
                    Text::make('Дата', 'date'),
                    Text::make('C', 'from_time'),
                    Text::make('До', 'to_time'),
                    Text::make('Пациент', 'patient_id'),
                 
                ]),
                HasMany::make('Отзывы', 'reviews', resource: ReviewResource::class)
                ->fields([
                    Text::make('Рейтинг', 'rating'),
                    Text::make('Отзыв', 'review'),
                    Text::make('Пациент', 'patient_id'),
                    Text::make('Дата создания', 'created_at'),
                ]),
        ];
    }

    
}
