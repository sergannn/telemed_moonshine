<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Article;

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
 * @extends ModelResource<CustomPost>
 */
class ArticleResource extends ModelResource
{
    protected string $model = Article::class;

    protected string $title = 'Articles';

    protected string $column = 'title';

  //  protected string $sortColumn = 'sorting';

    protected bool $createInModal = true;
  
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexFields(): array {
        return [
            ID::make()->sortable(),
            Text::make( 'Title' ),
            //    ->updateOnPreview(),
            Text::make( 'Description' )
                ->updateOnPreview(),
            Image::make( 'Image' ),
          //      ->updateOnPreview(),
            TinyMce::make( 'Html' ),
            BelongsTo::make( 'Category', 'category', 'title', resource: new CategoryResource() )
                ->nullable()
                ->badge(),
        ];
    }

    public function formFields(): array {
        return [
          //  Box::make( [
                ID::make()->sortable(),
                Text::make( 'Title' ),
               //     ->updateOnPreview(),
                Text::make( 'Description' )
                    ->updateOnPreview(),
                Image::make( 'Image' ),
                  //  ->updateOnPreview(),
                TinyMce::make( 'Html' ),
                BelongsTo::make( 'Category', 'category', 'title', resource:  new CategoryResource() )
                    ->nullable()
                    ->badge(),
         
        ];
    }

    public function detailFields(): array {
        return [
            ...$this->indexFields(),
        ];
    }

    /**
     * @param Story $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
