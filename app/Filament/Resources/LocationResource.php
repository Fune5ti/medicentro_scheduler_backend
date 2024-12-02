<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Configurações';

    protected static ?string $modelLabel = 'Local';

    protected static ?string $pluralModelLabel = 'Locais';

    protected static ?string $createButtonLabel = 'Novo Local';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('E-mail'),

                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255)
                            ->label('Endereço'),

                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(255)
                            ->label('Cidade'),

                        Forms\Components\TextInput::make('state')
                            ->required()
                            ->maxLength(255)
                            ->label('Estado'),

                        Select::make('doctor_ids')
                            ->relationship('doctors', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Médicos')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Nome'),
                                Forms\Components\TextInput::make('crm')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('CRM'),
                                Forms\Components\TextInput::make('phone')
                                    ->required()
                                    ->tel()
                                    ->maxLength(255)
                                    ->label('Telefone'),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->label('E-mail'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('E-mail'),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable()
                    ->label('Cidade'),

                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->sortable()
                    ->label('Estado'),

                Tables\Columns\TextColumn::make('doctors_count')
                    ->counts('doctors')
                    ->label('Médicos')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Atualizado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->options(fn() => Location::distinct()->pluck('state', 'state')->toArray())
                    ->multiple()
                    ->label('Estado'),

                Tables\Filters\SelectFilter::make('city')
                    ->options(fn() => Location::distinct()->pluck('city', 'city')->toArray())
                    ->multiple()
                    ->label('Cidade'),

                Tables\Filters\Filter::make('has_doctors')
                    ->query(fn(Builder $query): Builder => $query->has('doctors'))
                    ->label('Com Médicos'),

                Tables\Filters\Filter::make('no_doctors')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('doctors'))
                    ->label('Sem Médicos'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'view' => Pages\ViewLocation::route('/{record}'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
