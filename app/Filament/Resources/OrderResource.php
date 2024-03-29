<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Business Transactions';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'processing')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', '=', 'processing')->count() > 10
            ? 'warning'
            : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order Details')
                        ->schema([
                            Forms\Components\TextInput::make('receipt_no')
                                ->label('Reciept No.')
                                ->default('OR-' . random_int(100000, 9999999))
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ,

                            Forms\Components\Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('delivery_price')
                                ->label('Delivery Costs')
                                ->dehydrated()
                                ->numeric()
                                ->required(),

                            Forms\Components\Select::make('status')->label('Status')
                            ->options([
                                'pending' => OrderStatusEnum::PENDING->value,
                                'processing' => OrderStatusEnum::PROCESSING->value,
                                'completed' => OrderStatusEnum::COMPLETED->value,
                                'declined' => OrderStatusEnum::DECLINED->value,
                            ])->required(),

                            Forms\Components\MarkdownEditor::make('notes')
                                ->columnSpanFull()
                        ])->columns(2),
                    Forms\Components\Wizard\Step::make('Order Items')
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        ->options(Product::query()->pluck('name', 'id'))
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                        $set('unit_price', Product::find($state)?->selling_price ?? 0)),

                                    Forms\Components\TextInput::make('quantity')
                                        ->numeric()
                                        ->live()
                                        ->dehydrated()
                                        ->default(1)
                                        ->required(),

                                    Forms\Components\TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->dehydrated()
                                        ->live()
                                        ->default(0)
                                        ->numeric()
                                        ->required(),
                                        Forms\Components\TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->dehydrated()
                                        ->live()
                                        ->default(0)
                                        ->numeric()
                                        ->required(),

                                        Forms\Components\Placeholder::make('total')
                                        ->label('Item Total')
                                        ->dehydrated()
                                        ->default(0)
                                        ->disabled() // Disable user input since it's calculated
                                        ->content(function ($get) {
                                            $quantity = (int) $get('quantity');
                                            $unitPrice = (float) $get('unit_price');
                                    
                                            return $quantity * $unitPrice;
                                        })
                                        ->helperText(
                                            'This field is automatically calculated based on the Quantity and Unit Price.'
                                        )
                                    
                                ])->columns(5),
                                Forms\Components\Section::make('Overall')
                    ->schema([
                        Forms\Components\Placeholder::make("Price")
                                ->label("Price")
                                ->content(function ($get) {

                                    $CalculateTotal = collect($get('items'))->map(function ($item) {
                                        return $item['quantity'] * $item['unit_price'];
                                    })->sum();
                                    

                                    return $CalculateTotal;
                                }),
                            Forms\Components\Placeholder::make("Number of Items")
                                ->label("Number of Items")
                                ->content(function ($get) {
                                    return collect($get('items'))
                                        ->pluck('total')
                                        ->count();
                                })
                    ])->columnSpan(2),
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_no')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Reciept Number copied')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}