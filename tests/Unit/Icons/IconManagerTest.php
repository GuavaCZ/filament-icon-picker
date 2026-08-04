<?php

use Guava\IconPicker\Icons\Facades\IconManager;
use Guava\IconPicker\Icons\Icon;
use Guava\IconPicker\Icons\IconSet;

it('discovers the icon sets registered with blade-icons', function () {
    $sets = IconManager::getSets();

    expect($sets)->toHaveKey('heroicons')
        ->and($sets->get('heroicons'))->toBeInstanceOf(IconSet::class)
        ->and($sets->get('heroicons')->getPrefix())->toBe('heroicon')
    ;
});

it('resolves an existing icon', function () {
    $icon = IconManager::getIcon('heroicon-o-academic-cap');

    expect($icon)->toBeInstanceOf(Icon::class)
        ->and($icon->id)->toBe('heroicon-o-academic-cap')
        ->and($icon->getSet()->getId())->toBe('heroicons')
    ;
});

it('returns null for an icon whose set is not registered', function () {
    expect(IconManager::getIcon('not-a-registered-set-star'))->toBeNull();
});

it('returns null for a null icon', function () {
    expect(IconManager::getIcon(null))->toBeNull();
});

it('derives a human readable label from the icon name', function () {
    expect(IconManager::getIcon('heroicon-o-academic-cap')->label)->toBe('O academic cap');
});

it('returns null for an icon whose file does not exist', function () {
    expect(IconManager::getIcon('heroicon-o-this-icon-does-not-exist'))->toBeNull();
});

it('finds the set an icon belongs to from its prefix', function () {
    expect(IconManager::getSetFromIcon('heroicon-o-beaker')?->getId())->toBe('heroicons');
});

it('finds a set by prefix', function () {
    expect(IconManager::getSetByPrefix('heroicon')?->getId())->toBe('heroicons')
        ->and(IconManager::getSetByPrefix('nope'))->toBeNull()
    ;
});

it('lists the icons of a single set', function () {
    $icons = IconManager::getIcons('heroicons');

    expect($icons)->not->toBeEmpty()
        ->and($icons->every(fn (Icon $icon) => $icon->getSet()->getId() === 'heroicons'))->toBeTrue()
        ->and($icons->contains(fn (Icon $icon) => $icon->id === 'heroicon-o-academic-cap'))->toBeTrue()
    ;
});

it('accepts an IconSet instance when listing icons', function () {
    $set = IconManager::getSets()->get('heroicons');

    expect(IconManager::getIcons($set))->toEqual(IconManager::getIcons('heroicons'));
});
