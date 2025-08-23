<?php

namespace App\MoonShine\Resources;

use App\Models\Appointment;
use MoonShine\Laravel\Fields\Relationships\BelongsTo; 
use MoonShine\UI\Fields\DateTime;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Actions\DeleteAction;

class UpcomingAppointmentsResource extends ModelResource
{
    protected string $model = Appointment::class;

    public function indexFields(): array
    {
        return [
            ID::make(__('id'))->sortable(),
            BelongsTo::make(__('Doctor'), 'doctor','user.full_name', resource: DoctorResource::class),
            BelongsTo::make(__('Patient'), 'patient','user.full_name', resource: PatientResource::class),
            Text::make(__('Appointment Date'), 'date'),
            Text::make(__('From'), 'from_time'),
            Text::make(__('To'), 'to_time'),
            Text::make(__('Status'), 'status')->sortable(),
            Text::make(__('Тип'), 'description'),
        ];
    }

    public function actions(): array
    {
        return [
           // DeleteAction::make(),
        ];
    }

    public function title(): string
    {
        return __('Upcoming Appointments');
    }
}
