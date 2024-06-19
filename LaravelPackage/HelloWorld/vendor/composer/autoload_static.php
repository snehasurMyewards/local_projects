<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc11e2aafecfbe1d3dc9297ccd349def3
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bidis\\HelloWorld\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bidis\\HelloWorld\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc11e2aafecfbe1d3dc9297ccd349def3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc11e2aafecfbe1d3dc9297ccd349def3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc11e2aafecfbe1d3dc9297ccd349def3::$classMap;

        }, null, ClassLoader::class);
    }
}