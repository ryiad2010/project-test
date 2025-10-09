<?php
namespace Ryiad\FilamentToolkit;
use Filament\Panel;
use Ryiad\FilamentToolkit\Resources\UserResource;

class Toolkit implements \Filament\Contracts\Plugin
{
    protected bool $emailVerifiedAt = false;
    public static function make(): static
    {
        return new Toolkit();
    }
    public function emailVerifiedAt(bool $condition): static
    {
        $this->emailVerifiedAt = $condition;

        return $this;
    }
    public function hasEmailVerifiedAt(): bool
    {
        return $this->emailVerifiedAt;

    }
   public function getId(): string
   {

    return 'ryiad-toolkit';

   }
   public function register(Panel $panel): void
   {
       $panel
        ->resources([
                UserResource::class,
            ]);
   }
   public function boot(Panel $panel): void
   {
       //
   }
}
