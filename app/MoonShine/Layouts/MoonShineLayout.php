<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\SpecializationResource;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\Layouts\AppLayout;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Components\Layout\{Locales, Notifications, Profile, Search};
use MoonShine\UI\Components\{
    Breadcrumbs,
    Components,
    Layout\Flash,
    Layout\Div,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Footer,
    Layout\Head,
    Layout\Favicon,
    Layout\Assets,
    Layout\Meta,
    Layout\Header,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\TopBar,
    Layout\Wrapper,
    When
};
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;

use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\DoctorResource;
use App\MoonShine\Resources\DoctorSessionResource;
use App\MoonShine\Resources\PatientResource;
use App\MoonShine\Resources\StoryResource;
use App\MoonShine\Resources\RecommendationResource;
use App\MoonShine\Resources\SerUserResource;
use App\MoonShine\Resources\WeekDayResource;
use App\MoonShine\Resources\UpcomingAppointmentsResource;
use App\MoonShine\Resources\ReviewResource;
final class MoonShineLayout extends \MoonShine\Laravel\Layouts\AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }



    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    public function build(): Layout
    {
        return parent::build();
    }

    protected function menu(): array
    {
        return [
            MenuItem::make('Врачи', DoctorResource::class, 'user'),
            MenuItem::make('Пациенты', PatientResource::class, 'users'),
            MenuItem::make('Специализации', SpecializationResource::class, 'shield-check'),
            MenuItem::make('Сеансы', UpcomingAppointmentsResource::class, 'shield-check'),
             MenuItem::make( 'Категории статей', CategoryResource::class, 'book-open'),
            MenuItem::make( 'Stories', StoryResource::class, 'book-open'),
            MenuItem::make('Рекомендуем Вам', RecommendationResource::class, 'book-open'),
            MenuItem::make('Статьи', ArticleResource::class, 'newspaper'),
            MenuItem::make('Пользователи', SerUserResource::class,icon:'users'),
            MenuItem::make('Дни недели', WeekDayResource::class, 'calendar'),
            MenuGroup::make('Система', [
                MenuItem::make('Admins', MoonShineUserResource::class, 'users'),
                MenuItem::make('Roles', MoonShineUserRoleResource::class, 'shield-check'),
            ], 'cog-6-tooth'),
            MenuItem::make('Reviews', ReviewResource::class),
        ];
    }
}
