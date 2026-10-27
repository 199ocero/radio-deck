<?php

namespace JaOcero\RadioDeck;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RadioDeckServiceProvider extends PackageServiceProvider
{
    public static string $name = 'radio-deck';

    public static string $viewNamespace = 'radio-deck';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews(static::$viewNamespace)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->askToStarRepoOnGitHub('jaocero/radio-deck');
            });
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );
    }

    protected function getAssetPackageName(): ?string
    {
        return 'jaocero/radio-deck';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('radio-deck', __DIR__.'/../resources/dist/components/radio-deck.js'),
        ];
    }
}
