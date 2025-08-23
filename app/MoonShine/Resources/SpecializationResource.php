<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Specialization;

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
 * @extends ModelResource<Specialization>
 */
class SpecializationResource extends ModelResource
{
    protected string $model = Specialization::class;

    protected string $title = 'Specializations';

    protected string $column = 'title';

  //  protected string $sortColumn = 'sorting';

    protected bool $createInModal = true;
  
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexFields(): array {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
       //     HasMany::make('Врачи', 'doctors', resource: DoctorResource::class),
       //     Text::make('Описание', 'description'),
        ];
    }

    public function formFields(): array {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
          //12  Text::make('Описание', 'description'),
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
        ];
    }

    
}
