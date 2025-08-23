<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\User;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use  MoonShine\UI\Fields\Email;
use  MoonShine\UI\Fields\HiddenIds;
use  MoonShine\UI\Fields\ID;
use  MoonShine\UI\Fields\Password;
use  MoonShine\UI\Fields\PasswordRepeat;
use  MoonShine\UI\Fields\Text;
use VI\MoonShineSpatieMediaLibrary\Fields\MediaLibrary;

class SerUserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Users';

    public function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('first_name'),
            Text::make(label: 'last_name'),
            MediaLibrary::make('cover', 'profile')->multiple(),
            Email::make('Email'),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Text::make('first_name'),
                Text::make(label: 'last_name'),
                Email::make('Email'),
                MediaLibrary::make('cover', 'profile')->multiple(),
          
            ]),
        ];
    }

    public function detailFields(): iterable
    {
        return [
            ...$this->indexFields(),
        ];
    }

   
}
