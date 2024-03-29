<?php

declare(strict_types=1);
/**
 * This file is part of foo/bar.
 *
 * @link     https://github.com/huangdijia/nano-demo
 * @contact  Huangdijia <huangdijia@gmail.com>
 */
use Huangdijia\PhpCsFixer\Config;

require __DIR__ . '/vendor/autoload.php';

return (new Config())
    ->setHeaderComment(
        projectName: 'foo/bar',
        projectLink: 'https://github.com/huangdijia/nano-demo',
        contacts: [
            'Huangdijia' => 'huangdijia@gmail.com',
        ],
    )
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('public')
            ->exclude('runtime')
            ->exclude('vendor')
            ->in(__DIR__)
            ->append([
                __FILE__,
            ])
    )
    ->setUsingCache(false);
