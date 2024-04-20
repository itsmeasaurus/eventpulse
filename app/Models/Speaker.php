<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'qualification' => 'array'
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm() : array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Textarea::make('bio')
                ->required()
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->label('Twitter Profile')
                ->prefix('https://twitter.com/')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            CheckboxList::make('qualification')
                ->options([
                    'high-school-diploma' => 'High School Diploma',
                    'bachelor-degree-in-engineering' => 'Bachelor Degree in Engineering',
                    'nursing-license' => 'Nursing License',
                    'project-management-certification' => 'Project Management Certification',
                    'microsoft-office-specialist' => 'Microsoft Office Specialist',
                    'cisco-certified-network-associate' => 'Cisco Certified Network Associate',
                    'certified-financial-planner' => 'Certified Financial Planner',
                    'adobe-certified-associate' => 'Adobe Certified Associate',
                    'certified-scrummaster' => 'Certified ScrumMaster',
                    'google-ads-certification' => 'Google Ads Certification',
                    'six-sigma-green-belt' => 'Six Sigma Green Belt',
                    'aws-certified-developer' => 'AWS Certified Developer',
                    'comptia-security-certification' => 'CompTIA Security+ Certification',
                    'teaching-certificate' => 'Teaching Certificate',
                    'first-aid-certification' => 'First Aid Certification'
                ])
                ->columnSpanFull()
                ->columns(3)
                ->searchable()
        ];
    }
}
