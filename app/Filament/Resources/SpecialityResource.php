<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialityResource\Pages;
use App\Models\Speciality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

class SpecialityResource extends Resource
{
    protected static ?string $model = Speciality::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Serviços Médicos';

    protected static ?string $modelLabel = 'Especialidade';

    protected static ?string $pluralModelLabel = 'Especialidades';

    protected static ?string $createButtonLabel = 'Nova Especialidade';

    protected static ?string $recordTitleAttribute = 'Especialidade';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome'),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('CVE$')
                    ->maxValue(42949672.95)
                    ->default(0.00)
                    ->label('Preço'),

                Forms\Components\TextInput::make('estimated_time_in_minutes')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(30)
                    ->suffix('minutos')
                    ->label('Tempo Estimado'),

                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('Descrição'),

                Select::make('doctor_ids')
                    ->relationship('doctors', 'name')
                    ->multiple()
                    ->preload()
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
                            ->maxLength(255)
                            ->label('Telefone'),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->label('E-mail'),
                    ])
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

                Tables\Columns\TextColumn::make('price')
                    ->money('CVE')
                    ->sortable()
                    ->label('Preço'),

                Tables\Columns\TextColumn::make('estimated_time_in_minutes')
                    ->numeric()
                    ->suffix(' min')
                    ->sortable()
                    ->label('Tempo Estimado'),

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
                Tables\Filters\Filter::make('has_doctors')
                    ->query(fn(Builder $query): Builder => $query->has('doctors'))
                    ->label('Com Médicos Atribuídos'),

                Tables\Filters\Filter::make('no_doctors')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('doctors'))
                    ->label('Sem Médicos'),
            ])
            ->actions([
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
            'index' => Pages\ListSpecialities::route('/'),
            'create' => Pages\CreateSpeciality::route('/create'),
            'edit' => Pages\EditSpeciality::route('/{record}/edit'),
        ];
    }
}
