<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit90c1943acc81e10fae8db3c09a68f4c2
{
    public static $files = array (
        '9b38cf48e83f5d8f60375221cd213eee' => __DIR__ . '/..' . '/phpstan/phpstan/bootstrap.php',
        '38143a9afc50997d55e4815db8489d1c' => __DIR__ . '/..' . '/rector/rector/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
        'A' => 
        array (
            'Akyos\\PaymentBundle\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
        'Akyos\\PaymentBundle\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit90c1943acc81e10fae8db3c09a68f4c2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit90c1943acc81e10fae8db3c09a68f4c2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit90c1943acc81e10fae8db3c09a68f4c2::$classMap;

        }, null, ClassLoader::class);
    }
}
