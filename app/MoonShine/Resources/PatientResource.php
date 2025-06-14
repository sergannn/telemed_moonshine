<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;

use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;

use MoonShine\Fields\Image;
use MoonShine\Fields\Field;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Fields\Relationships\BelongsTo; 
use MoonShine\Components\MoonShineComponent;

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
            Text::make(
                'Description',
                'user.description',),
        ];
    }

    public function formFields(): array {
        return [
            ID::make()->sortable(),
           
            Text::make('First name', 'user.first_name',)
                ->onApply(function(Patient $item, $value) {
                    $item->user()->update(['first_name' => $value]);
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
    public function rules(Model $item): array
    {
        return [];
    }
}
