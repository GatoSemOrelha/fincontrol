<?php

namespace App\View;

use Illuminate\View\Compilers\BladeCompiler;

/**
 * Recompila views Blade a cada request no ambiente local.
 * Evita cache desatualizado com volumes Docker no Windows.
 */
class DevBladeCompiler extends BladeCompiler
{
    public function isExpired($path): bool
    {
        return true;
    }
}
