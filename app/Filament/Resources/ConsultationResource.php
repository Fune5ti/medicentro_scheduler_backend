<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultationResource\Pages;
use App\Models\Consultation;
use App\Models\DoctorAvailability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Filament\Support\Colors\Color;

class ConsultationResource extends Resource
{
    protected static ?string $model = Consultation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Agendamento';

    protected static ?string $navigationLabel = 'Consultas';

    protected static ?string $createButtonLabel = 'Nova Consulta';

    protected static ?string $modelLabel = 'Consulta';

    protected static ?string $pluralModelLabel = 'Consultas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_availability_id')
                    ->relationship('doctorAvailability', 'id', function ($query) {
                        return $query->with('doctor', 'serviceable');
                    })
                    ->getOptionLabelFromRecordUsing(fn(DoctorAvailability $record) =>
                    "Dr. {$record->doctor->name} - " .
                        ($record->serviceable_type === 'App\Models\Exam' ? 'Exame: ' : 'Especialidade: ') .
                        "{$record->serviceable->name} ({$record->available_date})")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->label('Disponibilidade do Médico'),

                Forms\Components\Select::make('patient_id')
                    ->relationship('patient', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Paciente')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('full_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome Completo'),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nif')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->required()
                            ->tel()
                            ->maxLength(255)
                            ->label('Telefone'),
                        Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->maxDate(now())
                            ->label('Data de Nascimento'),
                    ]),

                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->native(false)
                    ->live()
                    ->label('Hora de Início'),

                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->native(false)
                    ->after('start_time')
                    ->label('Hora de Término'),

                Forms\Components\Select::make('status')
                    ->options([
                        'scheduled' => 'Agendada',
                        'canceled' => 'Cancelada',
                        'completed' => 'Concluída',
                    ])
                    ->required()
                    ->default('scheduled')
                    ->label('Status'),
                Forms\Components\Textarea::make('notes')
                    ->label('Observações')
                    ->columnSpanFull()
                    ->rows(3),

                Forms\Components\FileUpload::make('file_path')
                    ->label('Arquivo')
                    ->directory('consultations')
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(10240) // 10MB
                    ->columnSpanFull()
                    ->storeFileNamesIn('file_name')
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            // If it's an array (multiple files), get the first one
                            $fileName = is_array($state) ? basename($state[0]) : basename($state);
                            $set('file_name', $fileName);
                        } else {
                            $set('file_name', null);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctorAvailability.doctor.name')
                    ->label('Médico')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('patient.full_name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('doctorAvailability.serviceable_type')
                    ->label('Tipo de Serviço')
                    ->formatStateUsing(
                        fn(string $state): string =>
                        str_contains($state, 'Exam') ? 'Exame' : 'Especialidade'
                    ),

                Tables\Columns\TextColumn::make('doctorAvailability.serviceable.name')
                    ->label('Serviço')
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctorAvailability.available_date')
                    ->label('Data')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Início')
                    ->time()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Término')
                    ->time()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'scheduled' => 'Agendada',
                        'completed' => 'Concluída',
                        'canceled' => 'Cancelada',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Observações')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('Arquivo')
                    ->searchable()
                    ->url(fn($record) => $record->file_path ? Storage::url($record->file_path) : null, true)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('doctor')
                    ->relationship('doctorAvailability.doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Médico'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Agendada',
                        'canceled' => 'Cancelada',
                        'completed' => 'Concluída',
                    ]),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('De'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Até'),
                    ])
                    ->label('Período')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'doctorAvailability',
                                    fn($query) => $query->whereDate('available_date', '>=', $date)
                                ),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'doctorAvailability',
                                    fn($query) => $query->whereDate('available_date', '<=', $date)
                                ),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->action(fn(Consultation $record) => $record->update(['status' => 'canceled']))
                    ->requiresConfirmation()
                    ->hidden(fn(Consultation $record) => $record->status !== 'scheduled')
                    ->color('danger'),
                Tables\Actions\Action::make('complete')
                    ->label('Concluir')
                    ->action(fn(Consultation $record) => $record->update(['status' => 'completed']))
                    ->requiresConfirmation()
                    ->hidden(fn(Consultation $record) => $record->status !== 'scheduled')
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('cancel')
                        ->label('Cancelar Selecionados')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'canceled']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Cancelar Consultas')
                        ->modalSubmitActionLabel('Sim, cancelar')
                        ->modalDescription('Tem certeza que deseja cancelar todas as consultas selecionadas?'),
                    Tables\Actions\BulkAction::make('complete')
                        ->label('Concluir Selecionados')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'completed']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Concluir Consultas')
                        ->modalSubmitActionLabel('Sim, concluir')
                        ->modalDescription('Tem certeza que deseja marcar todas as consultas selecionadas como concluídas?'),
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
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'edit' => Pages\EditConsultation::route('/{record}/edit'),
        ];
    }
}
