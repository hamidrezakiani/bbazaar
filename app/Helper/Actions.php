<?php
namespace App\Helper;

use Filament\Support\Colors\Color;
use Filament\Tables;

class Actions {


    public static function DeleteAction(): Tables\Actions\DeleteAction
    {
        return Tables\Actions\DeleteAction::make()
            ->button()
            ->tooltip('Delete')
            ->hiddenLabel()
            ->icon('tabler-trash')
            ->color(Color::Red);
    }
    public static function EditAction($record = null): Tables\Actions\EditAction
    {
        return Tables\Actions\EditAction::make()
            ->button()
            ->modalHeading(fn ($record) => "Edit {$record->title}")
            ->hiddenLabel()
            ->modalWidth('lg')
            ->tooltip('Edit');
    }

    public static function addLanguageAction($record = null): Tables\Actions\CreateAction
    {
        return Tables\Actions\CreateAction::make()
            ->modalWidth('lg')
            ->label('Language')
            ->icon('tabler-circle-plus');
    }

    public static function CustomDelete(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('Delete')
            ->button()
            ->tooltip('Delete')
            ->hiddenLabel()
            ->icon('tabler-trash')
            ->requiresConfirmation()
            ->color(Color::Red);
    }

    public static function ViewAction($record = null): Tables\Actions\ViewAction
    {
        return Tables\Actions\ViewAction::make()
            ->iconButton()
            ->hiddenLabel()
            ->button()
            ->color('primary')
            ->tooltip('View')
            ->icon('tabler-eye');
    }
}
