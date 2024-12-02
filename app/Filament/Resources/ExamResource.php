<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Serviços Médicos';

    protected static ?string $modelLabel = 'Exame';

    protected static ?string $pluralModelLabel = 'Exames';

    protected static ?string $createButtonLabel = 'Novo Exame';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full')
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
                            ->columnSpan('full')
                            ->label('Descrição'),

                        Select::make('doctor_ids')
                            ->relationship('doctors', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpan('full')
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
                            ]),
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

                Tables\Columns\TextColumn::make('price')
                    ->money('BRL')
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

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Excluído em'),
            ])
            ->filters([
                Tables\Filters\Filter::make('with_doctors')
                    ->query(fn(Builder $query): Builder => $query->has('doctors'))
                    ->label('Com Médicos Atribuídos'),

                Tables\Filters\Filter::make('without_doctors')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('doctors'))
                    ->label('Sem Médicos'),

                Tables\Filters\TrashedFilter::make()
                    ->label('Lixeira'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('Excluir Permanentemente'),
                Tables\Actions\RestoreAction::make()
                    ->label('Restaurar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Excluir Permanentemente'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar'),
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
