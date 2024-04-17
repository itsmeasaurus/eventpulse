<?php

namespace App\Filament\Resources;

use App\Enums\Region;
use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use App\Models\Conference;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Conference')
                    ->required()
                    ->maxLength(60),
                Forms\Components\Textarea::make('description')
                    ->label('What is the conference about?')
                    ->required(),
                Forms\Components\DateTimePicker::make('start_date')
                    ->native(false)
                    ->default(now())
                    ->closeOnDateSelection()
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'finished' => 'Finished',
                        'waiting' => 'Waiting',
                        'current' => 'Current'
                    ]),
                Forms\Components\Select::make('timezone')
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
                Forms\Components\Select::make('region')
                    ->live()
                    ->enum(Region::class)
                    ->options(Region::class),
                Forms\Components\Select::make('venue_id')
                    ->searchable()
                    ->preload()
                    ->createOptionForm(Venue::getForm())
                    ->relationship('venue', 'name', modifyQueryUsing: function(Builder $query, Get $get) {
                        return $query->where('region', $get('region'));
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('timezone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
