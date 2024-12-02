<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Exam;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Speciality;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\DoctorAvailability;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DoctorAvailabilityResource\Pages;
use App\Filament\Widgets\DoctorAvailabilityWidget;

class DoctorAvailabilityResource extends Resource
{
    protected static ?string $model = DoctorAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Agendamento';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Disponibilidade';

    protected static ?string $pluralModelLabel = 'Disponibilidades';

    protected static ?string $createButtonLabel = 'Nova Disponibilidade';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Médico')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Período')
                    ->schema([
                        Forms\Components\DatePicker::make('date_range_from')
                            ->required()
                            ->minDate(now())
                            ->native(false)
                            ->label('De'),

                        Forms\Components\DatePicker::make('date_range_to')
                            ->required()
                            ->minDate(now())
                            ->native(false)
                            ->label('Até'),

                        Forms\Components\CheckboxList::make('weekdays')
                            ->options([
                                '1' => 'Segunda-feira',
                                '2' => 'Terça-feira',
                                '3' => 'Quarta-feira',
                                '4' => 'Quinta-feira',
                                '5' => 'Sexta-feira',
                                '6' => 'Sábado',
                                '0' => 'Domingo',
                            ])
                            ->label('Dias da Semana')
                            ->required()
                            ->columns(4),
                    ]),

                Forms\Components\Section::make('Horário')
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->required()
                            ->native(false)
                            ->label('Hora de Início'),

                        Forms\Components\TimePicker::make('end_time')
                            ->required()
                            ->native(false)
                            ->after('start_time')
                            ->label('Hora de Término'),
                    ])->columns(2),

                Forms\Components\Section::make('Serviço')
                    ->schema([
                        Forms\Components\Select::make('serviceable_type')
                            ->label('Tipo de Serviço')
                            ->options([
                                Exam::class => 'Exame',
                                Speciality::class => 'Especialidade',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('serviceable_id')
                            ->label('Serviço')
                            ->options(function (Get $get) {
                                $type = $get('serviceable_type');
                                $doctorId = $get('doctor_id');

                                if (!$type || !$doctorId) {
                                    return [];
                                }

                                if ($type === Exam::class) {
                                    return Exam::whereHas('doctors', function ($query) use ($doctorId) {
                                        $query->where('doctors.id', $doctorId);
                                    })->pluck('name', 'id');
                                }

                                if ($type === Speciality::class) {
                                    return Speciality::whereHas('doctors', function ($query) use ($doctorId) {
                                        $query->where('doctors.id', $doctorId);
                                    })->pluck('name', 'id');
                                }

                                return [];
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Médico')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_date')
                    ->date()
                    ->sortable()
                    ->label('Data Disponível'),

                Tables\Columns\TextColumn::make('start_time')
                    ->time()
                    ->sortable()
                    ->label('Início'),

                Tables\Columns\TextColumn::make('end_time')
                    ->time()
                    ->sortable()
                    ->label('Término'),

                Tables\Columns\TextColumn::make('serviceable_type')
                    ->label('Tipo de Serviço')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        Exam::class => 'Exame',
                        Speciality::class => 'Especialidade',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('serviceable.name')
                    ->label('Serviço')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('doctor')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Médico'),

                Tables\Filters\Filter::make('available_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('De'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('available_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('available_date', '<=', $date),
                            );
                    })
                    ->label('Período'),

                Tables\Filters\SelectFilter::make('service_type')
                    ->options([
                        Exam::class => 'Exame',
                        Speciality::class => 'Especialidade',
                    ])
                    ->label('Tipo de Serviço'),
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
            'index' => Pages\ListDoctorAvailabilities::route('/'),
            'create' => Pages\CreateDoctorAvailability::route('/create'),
            'edit' => Pages\EditDoctorAvailability::route('/{record}/edit'),
        ];
    }



    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
