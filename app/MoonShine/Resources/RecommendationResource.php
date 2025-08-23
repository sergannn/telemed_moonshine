<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Recommendation;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\UI\Fields\ID;

use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\Laravel\Fields\Relationships\BelongsTo; 
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Fields\RangeSlider; 
/**
 * @extends ModelResource<Recommendation>
 */
class RecommendationResource extends ModelResource
{
    protected string $model = Recommendation::class;

    protected string $title = 'Рекомендуем Вам';

    protected string $column = 'title';

    protected bool $createInModal = true;
  
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexFields(): array {
        return [
            ID::make()->sortable(),
            Text::make( 'Title' ),
            Text::make( 'Description' )
                ->updateOnPreview(),
            Image::make( 'Image' ),
            Number::make('Duration'),
          
            BelongsTo::make( 'Category', 'category', 'title', resource: CategoryResource::class )
                ->nullable()
                ->badge(),
        ];
    }

    public function formFields(): array {
        return [
            ID::make()->sortable(),
            Text::make( 'Title' ),
            Text::make( 'Description' )
                ->updateOnPreview(),
            Image::make( 'Image' ),
            TinyMce::make( 'Html' ),
            Number::make('Duration')->buttons(),
            
            BelongsTo::make( 'Category', 'category', 'title', resource: CategoryResource::class )
                ->nullable()
                ->badge(),
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
        ];
    }
}
