<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Lab404\Impersonate\Models\Impersonate;


use Laravel\Sanctum\HasApiTokens as HasApiTokensTrait;
use Laravel\Sanctum\Contracts\HasApiTokens;

class UserQ extends Authenticatable implements  HasApiTokens, MustVerifyEmail
{
    use HasApiTokensTrait, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact',
        'dob',
        'gender',
        'status',
        'password',
        'language',
        'blood_group',
        'type',
        'region_code',
        'email_verified_at',
        'email_notification',
        'time_zone',
        'dark_mode',
        'description', // Add this line
    ];

    const LANGUAGES = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'ru' => 'Russian',
        'pt' => 'Portuguese',
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'tr' => 'Turkish',
        'it' => 'Italian',
    ];

    const LANGUAGES_IMAGE = [
        'en' => 'web/media/flags/united-states.svg',
        'es' => 'web/media/flags/spain.svg',
        'fr' => 'web/media/flags/france.svg',
        'de' => 'web/media/flags/germany.svg',
        'ru' => 'web/media/flags/russia.svg',
        'pt' => 'web/media/flags/portugal.svg',
        'ar' => 'web/media/flags/iraq.svg',
        'zh' => 'web/media/flags/china.svg',
        'tr' => 'web/media/flags/turkey.svg',
        'it' => 'web/media/flags/italy.svg',
    ];

    const TIME_ZONE_ARRAY = [
       
        'Europe/Moscow',
       
    ];

    const PROFILE = 'profile';

    const ADMIN = 1;
    const DOCTOR = 2;
    const PATIENT = 3;
    const STAFF = 4;

    const TYPE = [
        self::ADMIN => 'Admin',
        self::DOCTOR => 'Doctor',
        self::PATIENT => 'Patient',
        self::STAFF => 'Staff',
    ];

    const ALL = 2;
    const ACTIVE = 1;
    const DEACTIVE = 0;

    const STATUS = [
        self::DEACTIVE => 'Deactive',
        self::ACTIVE => 'Active',
        self::ALL => 'All',
    ];

    protected $appends = ['full_name', 'profile_image', 'role_name', 'role_display_name'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    const MALE = 1;
    const FEMALE = 2;

    const GENDER = [
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'contact' => 'string',
        'dob' => 'string',
        'gender' => 'integer',
        'status' => 'boolean',
        'password' => 'string',
        'language' => 'string',
        'blood_group' => 'string',
        'type' => 'integer',
        'region_code' => 'string',
        'email_notification' => 'boolean',
        'time_zone' => 'string',
        'dark_mode' => 'boolean',
        'description' => 'string', // Add this line
    ];

    public function getProfileImageAttribute(): string
    {
        /** @var Media $media */
        $media = $this->getMedia(self::PROFILE)->first();
        if (!empty($media)) {
            return $media->getFullUrl();
        }
        $gender = $this->gender;
        if ($gender == self::FEMALE) {
            return asset('web/media/avatars/female.png');
        }

        return asset('web/media/avatars/male.png');
    }

    public function getRoleNameAttribute()
    {
        $role = $this->roles->first();
        if (!empty($role)) {
            return $role->display_name;
        }
    }

    public function getRoleDisplayNameAttribute()
    {
        $role = $this->roles->first();
        if (!empty($role)) {
            return $role->name;
        }
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'owner');
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class, 'user_id');
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class, 'user_id');
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class, 'user_id');
    }

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function gCredentials(): HasOne
    {
        return $this->hasOne(GoogleCalendarIntegration::class, 'user_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PROFILE)
            ->singleFile()
            ->useDisk('public');
    }
}
