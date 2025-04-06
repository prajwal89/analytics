<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Prajwal89\Analytics\Filament\Resources\BiasResource\Pages\CreateBias;
use Prajwal89\Analytics\Filament\Resources\BiasResource\Pages\EditBias;
use Prajwal89\Analytics\Filament\Resources\BiasResource\Pages\ListBias;
use Prajwal89\Analytics\Filament\Resources\BiasResource\Widgets\BiasTrendChart;
use Prajwal89\Analytics\Models\Bias;

class BiasResource extends Resource
{
    protected static ?string $model = Bias::class;

    // protected static ?string $navigationIcon = 'heroicon-o-forward';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('biasable1_type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('biasable1_id')
                    ->required()
                    ->numeric(),
                TextInput::make('biasable1_route_name')
                    ->maxLength(255),
                TextInput::make('biasable2_type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('biasable2_id')
                    ->required()
                    ->numeric(),
                TextInput::make('biasable2_route_name')
                    ->maxLength(255),
                TextInput::make('bias')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('last_session_id')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('biasable1_type')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return class_basename($record->biasable1_type);
                    }),
                TextColumn::make('biasable1_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('biasable1_route_name')
                    ->label('B1 Route')
                    ->searchable(),
                TextColumn::make('biasable2_type')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return class_basename($record->biasable2_type);
                    }),
                TextColumn::make('biasable2_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('biasable2_route_name')
                    ->label('B2 Route')
                    ->searchable(),
                TextColumn::make('bias')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_session_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('biasable_1')
                    ->label('Biasable 1')
                    ->searchable()
                    ->query(function ($query, array $data) {
                        if ($data['value'] === null) {
                            return $query;
                        }

                        [$biasable1_type, $biasable1_id] = explode(':', $data['value']);

                        return $query
                            ->where('biasable1_type', $biasable1_type)
                            ->where('biasable1_id', $biasable1_id);
                    })
                    ->getSearchResultsUsing(function ($query) {
                        $searchTerm = $query;

                        return Bias::query()
                            ->with('biasable1')
                            ->select('biasable1_type', 'biasable1_id')
                            ->whereNotNull('biasable1_id')
                            ->whereNotNull('biasable1_type')
                            ->where(function ($query) use ($searchTerm): void {
                                $query->whereHasMorph('biasable1', array_keys(config('analytics.biasable')), function ($morphQuery, $type) use ($searchTerm): void {
                                    $currentBiasable = config('analytics.biasable')[$type];
                                    $morphQuery->where($currentBiasable['searchable_using'], 'like', "%{$searchTerm}%");
                                });
                            })
                            ->get()
                            ->map(function (Bias $bias) {
                                $biasable = $bias->biasable1;

                                if ($biasable === null) {
                                    return null;
                                }

                                $currentBiasable = config('analytics.biasable')[get_class($biasable)];

                                $name = $biasable->{$currentBiasable['searchable_using']};

                                return [
                                    get_class($biasable) . ':' . $biasable->getKey() => class_basename(get_class($biasable)) . ': ' . $name,
                                ];
                            });
                    }),

                SelectFilter::make('biasable_2')
                    ->label('Biasable 2')
                    ->searchable()
                    ->query(function ($query, array $data) {
                        if ($data['value'] === null) {
                            return $query;
                        }

                        [$biasable2_type, $biasable2_id] = explode(':', $data['value']);

                        return $query
                            ->where('biasable2_type', $biasable2_type)
                            ->where('biasable2_id', $biasable2_id);
                    })
                    ->getSearchResultsUsing(function ($query) {
                        $searchTerm = $query;

                        return Bias::query()
                            ->with('biasable2')
                            ->select('biasable2_type', 'biasable2_id')
                            ->whereNotNull('biasable2_id')
                            ->whereNotNull('biasable2_type')
                            ->where(function ($query) use ($searchTerm): void {
                                $query->whereHasMorph('biasable2', array_keys(config('analytics.biasable')), function ($morphQuery, $type) use ($searchTerm): void {
                                    $currentBiasable = config('analytics.biasable')[$type];
                                    $morphQuery->where($currentBiasable['searchable_using'], 'like', "%{$searchTerm}%");
                                });
                            })
                            ->get()
                            ->map(function (Bias $bias) {
                                $biasable = $bias->biasable2;

                                if ($biasable === null) {
                                    return null;
                                }

                                $currentBiasable = config('analytics.biasable')[get_class($biasable)];

                                $name = $biasable->{$currentBiasable['searchable_using']};

                                return [
                                    get_class($biasable) . ':' . $biasable->getKey() => class_basename(get_class($biasable)) . ': ' . $name,
                                ];
                            });
                    }),

                SelectFilter::make('biasable1_route_name')
                    ->label('B1 route name')
                    ->searchable()
                    ->options(function () {
                        return Bias::query()
                            ->select(['biasable1_route_name', 'created_at'])
                            ->distinct('biasable1_route_name')
                            ->latest()
                            ->pluck('biasable1_route_name')
                            ->filter()
                            ->mapWithKeys(function ($source) {
                                return [
                                    $source => $source,
                                ];
                            });
                    }),
                SelectFilter::make('biasable2_route_name')
                    ->label('B2 route name')
                    ->searchable()
                    ->options(function () {
                        return Bias::query()
                            ->select(['biasable2_route_name', 'created_at'])
                            ->distinct('biasable2_route_name')
                            ->latest()
                            ->pluck('biasable2_route_name')
                            ->filter()
                            ->mapWithKeys(function ($source) {
                                return [
                                    $source => $source,
                                ];
                            });
                    }),
                DateRangeFilter::make('created_at'),
            ], FiltersLayout::AboveContent)
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BiasTrendChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBias::route('/'),
            'create' => CreateBias::route('/create'),
            'edit' => EditBias::route('/{record}/edit'),
        ];
    }
}
