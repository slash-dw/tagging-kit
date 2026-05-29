<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use SlashDw\TaggingKit\Http\Controllers\ConfigController;
use SlashDw\TaggingKit\Http\Controllers\DeleteUserTagController;
use SlashDw\TaggingKit\Http\Controllers\SuggestTagController;

Route::get('config', ConfigController::class)->name('config');

Route::get('suggest', SuggestTagController::class)
    ->name('suggest')
    ->middleware('throttle:'.config('tagging-kit.throttle.suggest', '60,1'));

Route::delete('tags/{tag}', DeleteUserTagController::class)
    ->name('tags.destroy')
    ->middleware('throttle:'.config('tagging-kit.throttle.delete_user_tag', '30,1'));
