<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Prajwal89\Analytics\Enums\CountryCode;
use Prajwal89\Analytics\Filament\Resources\PageViewResource\Pages;
use Prajwal89\Analytics\Filament\Resources\PageViewResource\Pages\ListPageViews;
use Prajwal89\Analytics\Filament\Resources\PageViewResource\Pages\PageAnalyticsPage;
use Prajwal89\Analytics\Filament\Resources\PageViewResource\Widgets\PageViewsTrendChart;
use Prajwal89\Analytics\Models\PageView;

class PageViewResource extends Resource
{
    protected static ?string $model = PageView::class;

    // protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Page Views';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->sortable()
                //     ->searchable(),
                TextColumn::make('path')
                    ->sortable()
                    ->searchable()
                    ->limit(30),
                TextColumn::make('route_name')
                    ->label('Route')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('country_code')
                    ->label('Country')
                    // ->icon(function ($record) {
                    //     if ($record->country_code) {
                    //         return "flags.4x3." . strtolower($record->country_code);
                    //     }
                    //     return 'heroicon-o-flag';
                    // })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label('City')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('device')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('browser')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('time_on_page')
                    ->numeric()
                    ->suffix(' sec')
                    ->sortable(),
                TextColumn::make('scroll_depth')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('viewable_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('viewable_type')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('device')
                    ->options(fn() => ['' => 'Select Device'] + PageView::query()->distinct()->pluck('device', 'device')->toArray()),

                SelectFilter::make('browser')
                    ->options(fn() => ['' => 'Select Browser'] + PageView::query()->distinct()->pluck('browser', 'browser')->toArray()),

                SelectFilter::make('country_code')
                    ->options(CountryCode::class),

                DateRangeFilter::make('created_at'),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Action::make('analytics')
                    ->icon('heroicon-o-chart-bar')
                    // ->success()
                    ->url(function ($record) {
                        return self::getUrl('analytics');
                    })
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

    public static function getPages(): array
    {
        return [
            'index' => ListPageViews::route('/'),
            'analytics' => PageAnalyticsPage::route('/analytics')
            // 'create' => Pages\CreatePageView::route('/create'),
            // 'edit' => Pages\EditPageView::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PageViewsTrendChart::class,
        ];
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
