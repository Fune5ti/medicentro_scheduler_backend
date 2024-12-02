<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Equipe Médica';

    protected static ?string $modelLabel = 'Médico';

    protected static ?string $pluralModelLabel = 'Médicos';

    protected static ?string $createButtonLabel = 'Novo Médico';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome'),

                Forms\Components\TextInput::make('crm')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('CRM'),

                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->maxLength(255)
                    ->label('Telefone'),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('E-mail'),

                FileUpload::make('photo_location')
                    ->image()
                    ->directory('doctors')
                    ->visibility('public')
                    ->imageEditor()
                    ->circleCropper()
                    ->label('Foto'),

                Select::make('location_ids')
                    ->relationship('locations', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ->label('Locais de Atendimento'),

                Select::make('speciality_ids')
                    ->relationship('specialities', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Especialidades'),

                Select::make('exam_ids')
                    ->relationship('exams', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Exames'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_location')
                    ->circular()
                    ->label('Foto'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nome'),

                Tables\Columns\TextColumn::make('crm')
                    ->searchable()
                    ->label('CRM'),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Telefone'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('E-mail'),

                Tables\Columns\TextColumn::make('locations.name')
                    ->badge()
                    ->searchable()
                    ->label('Locais'),

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
                //
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
