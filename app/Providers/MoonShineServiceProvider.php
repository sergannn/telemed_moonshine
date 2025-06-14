<?php

declare(strict_types=1);

namespace App\Providers;

use MoonShine\Providers\MoonShineApplicationServiceProvider;
use MoonShine\MoonShine;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Menu\MenuElement;
use MoonShine\Pages\Page;
use Closure;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\StoryResource;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\DoctorResource;
use App\MoonShine\Resources\PatientResource;
use App\MoonShine\Resources\WeekDayResource;

class MoonShineServiceProvider extends MoonShineApplicationServiceProvider
{
    /**
     * @return list<ResourceContract>
     */
    protected function resources(): array
    {
        return [];
    }

    /**
     * @return list<Page>
     */
    protected function pages(): array
    {
        return [];
    }

    /**
     * @return Closure|list<MenuElement>
     */
    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn() => __('moonshine::ui.resource.system'), [
                MenuItem::make(
                    static fn() => __('moonshine::ui.resource.admins_title'),
                    new MoonShineUserResource()
                ),
                MenuItem::make(
                    static fn() => __('moonshine::ui.resource.role_title'),
                    new MoonShineUserRoleResource()
                ),
            ]),

            MenuItem::make('Documentation', 'https://moonshine-laravel.com/docs')
                ->badge(fn() => 'Check')
                ->blank(),
                MenuItem::make("Общее",''),
                MenuItem::make('Stories', new StoryResource)->icon('heroicons.newspaper') ,
                MenuItem::make('Articles', new ArticleResource)->icon('heroicons.queue-list'),
                MenuItem::make('Doctors', new DoctorResource)->icon('heroicons.queue-list'),
                MenuItem::make('Patients', new PatientResource)->icon('heroicons.queue-list'),
                MenuItem::make('Days', filler: new WeekDayResource)->icon('heroicons.calendar'),
      
        ];
    }

    /**
     * @return Closure|array{css: string, colors: array, darkColors: array}
     */
    protected function theme(): array
    {
        return [];
    }
}
