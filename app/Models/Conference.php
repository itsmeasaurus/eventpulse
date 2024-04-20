<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'venue_id' => 'integer',
        'region' => Region::class
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->label('Conference')
                ->required()
                ->maxLength(60),
            Textarea::make('description')
                ->label('What is the conference about?')
                ->required(),
            DateTimePicker::make('start_date')
                ->native(false)
                ->default(now())
                ->closeOnDateSelection()
                ->required(),
            DateTimePicker::make('end_date')
                ->native(false)
                ->closeOnDateSelection()
                ->required(),
            Select::make('status')
                ->required()
                ->options([
                    'finished' => 'Finished',
                    'waiting' => 'Waiting',
                    'current' => 'Current'
                ]),
            Select::make('timezone')
                ->required()
                ->options([
                    "utc" => "UTC",
                    "gmt" => "GMT",
                    "bst" => "BST",
                    "cet" => "CET",
                    "cest" => "CEST",
                    "eet" => "EET",
                    "eest" => "EEST",
                    "est" => "EST",
                    "edt" => "EDT",
                    "cst" => "CST",
                    "cdt" => "CDT",
                    "mst" => "MST",
                    "mdt" => "MDT",
                    "pst" => "PST",
                    "pdt" => "PDT",
                ]),
            Select::make('region')
                ->live()
                ->enum(Region::class)
                ->options(Region::class),
            Select::make('venue_id')
                ->searchable()
                ->preload()
                ->relationship('venue', 'name', modifyQueryUsing: function(Builder $query, Get $get) {
                    return $query->where('region',$get('region'));
                })
                ->createOptionForm(
                    function(Get $get) {
                        return [
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('city')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('postal_code')
                                ->required()
                                ->maxLength(255),
                            Select::make('region')
                                ->enum(Region::class)
                                ->options(Region::class)
                                ->default($get('region'))
                        ];
                    }
                ),
            CheckboxList::make('speakers')
                ->relationship('speakers','name')
                ->options(Speaker::all()->pluck('name','id'))
        ];
    }
}
