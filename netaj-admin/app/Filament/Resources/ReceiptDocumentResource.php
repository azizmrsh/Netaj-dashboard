<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptDocumentResource\Pages;
use App\Filament\Resources\ReceiptDocumentResource\RelationManagers;
use App\Models\ReceiptDocument;
use App\Models\Supplier;
use App\Models\Transporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceiptDocumentResource extends Resource
{
    protected static ?string $model = ReceiptDocument::class;

     protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Document Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('date_and_time')
                            ->required()
                            ->label('Date and Time'),
                        Forms\Components\Select::make('id_supplier')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Supplier'),
                        Forms\Components\Select::make('id_transporter')
                            ->relationship('transporter', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Transporter'),
                        Forms\Components\TextInput::make('purchase_invoice_no')
                            ->label('Purchase Invoice Number'),
                        Forms\Components\TextInput::make('material_source')
                            ->label('Material Source'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Officer Information')
                    ->schema([
                        Forms\Components\TextInput::make('purchasing_officer_name')
                            ->label('Purchasing Officer Name'),
                        Forms\Components\Textarea::make('purchasing_officer_signature')
                            ->label('Purchasing Officer Signature'),
                        Forms\Components\TextInput::make('warehouse_officer_name')
                            ->label('Warehouse Officer Name'),
                        Forms\Components\Textarea::make('warehouse_officer_signature')
                            ->label('Warehouse Officer Signature'),
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Recipient Name'),
                        Forms\Components\Textarea::make('recipient_signature')
                            ->label('Recipient Signature'),
                        Forms\Components\TextInput::make('accountant_name')
                            ->label('Accountant Name'),
                        Forms\Components\Textarea::make('accountant_signature')
                            ->label('Accountant Signature'),
                    ])->columns(2)->collapsible(),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_and_time')
                    ->dateTime()
                    ->sortable()
                    ->label('Date & Time'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->sortable()
                    ->searchable()
                    ->label('Supplier'),
                Tables\Columns\TextColumn::make('transporter.name')
                    ->sortable()
                    ->searchable()
                    ->label('Transporter'),
                Tables\Columns\TextColumn::make('purchase_invoice_no')
                    ->searchable()
                    ->label('Invoice Number'),
                Tables\Columns\TextColumn::make('material_source')
                    ->searchable()
                    ->label('Material Source'),
                Tables\Columns\TextColumn::make('purchasing_officer_name')
                    ->searchable()
                    ->toggledHiddenByDefault()
                    ->label('Purchasing Officer'),
                Tables\Columns\TextColumn::make('warehouse_officer_name')
                    ->searchable()
                    ->toggledHiddenByDefault()
                    ->label('Warehouse Officer'),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable()
                    ->toggledHiddenByDefault()
                    ->label('Recipient'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault(),
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
            'index' => Pages\ListReceiptDocuments::route('/'),
            'create' => Pages\CreateReceiptDocument::route('/create'),
            'edit' => Pages\EditReceiptDocument::route('/{record}/edit'),
        ];
    }
}
