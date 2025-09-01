<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Article;

use MoonShine\Laravel\Resources\ModelResource;

use MoonShine\Decorations\Block;
use MoonShine\UI\Fields\ID;

use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\Laravel\Fields\Relationships\BelongsTo; 
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\Layout\Html;

use MoonShine\UI\Components\Layout\Body;
use MoonShine\UI\Components\Layout\Head;
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
//                TinyMce::make( 'Html' ),
            Text::make( 'Video URL', 'video_url' )
                ->updateOnPreview(),
            BelongsTo::make( 'Category', 'category', 'title', resource:  CategoryResource::class   )
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
                Text::make( 'Video URL', 'video_url' )
                    ->updateOnPreview(),
                BelongsTo::make( 'Category', 'category', 'title', resource:   CategoryResource::class )
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
    public function rules(mixed $item): array
    {
        return [];
    }
}
