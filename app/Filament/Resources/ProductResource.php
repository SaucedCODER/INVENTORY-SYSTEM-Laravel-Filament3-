<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                        ->required()
                        ->disabled(),
                        Forms\Components\MarkdownEditor::make('description')
                        ->columnSpan('full'),
                    ])->columns(2),
                    Forms\Components\Section::make('Pricing & Stocks')->schema([
                        Forms\Components\TextInput::make('sku')
                        ->label('SKU')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                        Forms\Components\TextInput::make('buying_price')
                        ->required()
                        ->numeric(),
                        Forms\Components\TextInput::make('selling_price')
                        ->required()
                        ->numeric(),
                    ])->columns(2),
                    Forms\Components\Section::make('Other Details')->schema([
                        Forms\Components\Select::make('unit_id')
                        ->label('Unit')
                        ->required()
                        ->multiple()
                        ->relationship('units', 'name')->searchable(),
                        Forms\Components\TextInput::make('size_id')
                            ->numeric(),
                    ])->columns(2),
                    
                ]),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Status')->schema([
                    Forms\Components\Toggle::make('is_visible')
                    ->required(),
                    Forms\Components\Toggle::make('is_featured')
                    ->required(),
                    Forms\Components\DatePicker::make('published_at')
                    ->required()->columnSpan('full'),
                    ])->columns(2),
                ]),
                // Forms\Components\TextInput::make('brand_id')
                //     ->required()
                //     ->numeric(),
             
            //     Forms\Components\Select::make('brand_id')
            //     ->relationship('brand', 'name')
            //     ->required(),

            // Forms\Components\Select::make('categories')
            //     ->relationship('categories', 'name')
            //     ->multiple()
            //     ->required(),
                
                // Forms\Components\FileUpload::make('image')
                //     ->image()
                //     ->required(),
                
             
              
          
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buying_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
