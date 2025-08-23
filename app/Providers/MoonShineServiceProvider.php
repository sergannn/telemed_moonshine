<?php

declare(strict_types=1);

namespace App\Providers;

use App\MoonShine\Resources\SpecializationResource;
use App\MoonShine\Resources\UpcomingAppointmentsResource;
use App\MoonShine\Resources\UserResourceSer;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Laravel\Resources\MoonShineUserResource;
use App\MoonShine\Laravel\Resources\MoonShineUserRoleResource;

use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\DoctorResource;

use App\MoonShine\Resources\DoctorSessionResource;
use App\MoonShine\Resources\PatientResource;
use App\MoonShine\Resources\StoryResource;
use App\MoonShine\Resources\RecommendationResource;
use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\SerUserResource;
use App\MoonShine\Resources\WeekDayResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(CoreContract $core, ConfiguratorContract $config): void
    {
        $core
            ->resources([
                \MoonShine\Laravel\Resources\MoonShineUserResource::class,
                \MoonShine\Laravel\Resources\MoonShineUserRoleResource::class,
                ArticleResource::class,
                CategoryResource::class,
DoctorResource::class,
SpecializationResource::class,
DoctorSessionResource::class,

UpcomingAppointmentsResource::class,

PatientResource::class,
                StoryResource::class,
                RecommendationResource::class,
                UserResource::class,
WeekDayResource::class,
SerUserResource::class,///
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }


}
