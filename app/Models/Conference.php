<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
            Section::make('Conference Details')
                ->collapsible()
                ->description('Provide information about the conference')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->label('Conference')
                        ->required()
                        ->maxLength(60),
                    Textarea::make('description')
                        ->columnSpanFull()
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
                    Fieldset::make('Status')
                        ->schema([
                            Toggle::make('is_published')
                                ->label('Is publish?'),
                            Select::make('status')
                                ->required()
                                ->options([
                                    'draft' => 'Draft',
                                    'pending' => 'Pending',
                                    'finished' => 'Finished'
                                ]),
                        ]),
                    
            ]),
            Actions::make([
                Action::make('Generate Data')
                    ->visible(function (string $operation) {
                        if($operation !== 'create') {
                            return false;
                        }
                        if(! app()->environment('local')) {
                            return false;
                        }
                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ]),
            Section::make('Time and Location')
                ->schema([
                    Select::make('timezone')
                        ->required()
                        ->options([
                            "utc" => "UTC",
                            "gmt" => "GMT",
                            "bst" => "BST",
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
                    
                 ]),
            Section::make('Speaker Information')
                ->schema([
                    CheckboxList::make('speakers')
                    ->relationship('speakers','name')
                    ->options(Speaker::all()->pluck('name','id'))
                ])
        ];
    }
}
