<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\UI\Fields\ID;

use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\Laravel\Fields\Relationships\BelongsTo; 
use MoonShine\UI\Components\MoonShineComponent;
use VI\MoonShineSpatieMediaLibrary\Fields\MediaLibrary;
/**
 * @extends ModelResource<Patient>
 */
class PatientResource extends ModelResource
{
    protected string $model = Patient::class;

    protected string $title = 'Patients';

    protected string $column = 'title';

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
                Text::make('Фамилия'    , 'user.last_name',),
            Text::make(
                'E-mail',
                'user.email',),
                Text::make('Время создания'    , 'user.created_at',),//->sortable(),
                Text::make('Время обновления'    , 'updated_at',)->sortable(),
            Text::make('Снилс','user.snils')->sortable(),    
            Text::make(
                'Описание',
                'user.description',),
                MediaLibrary::make('Фото', 'profile'),
        ];
    }

    public function formFields(): array {
        return [
            ID::make()->sortable(),
           
            Text::make('Имя', 'user.first_name',)
                ->onApply(function(Patient $item, $value) {
                    $item->user()->update(['first_name' => $value]);
                    return $item;
                }),
                Text::make('Фамилия'    , 'user.last_name',)
                ->onApply(function(Patient $item, $value) {
                    $item->user()->update(['last_name' => $value]);
                    return $item;
                }),
            Text::make('Description', 'user.description',)
                ->onApply(function(Patient $item, $value) {
                    $item->user()->update(['description' => $value]);
                    return $item;
                })
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
        ];
    }

    /**
     * @param Patient $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */

}
