<?php

declare(strict_types=1);
/**
 * This file is part of nano-demo.
 *
 * @link     https://github.com/huangdijia/nano-demo
 * @contact  Huangdijia <huangdijia@gmail.com>
 */
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\ClassLoader;
use Hyperf\Nano\Factory\AppFactory;
use Hyperf\Utils\Composer;
use Psr\Log\LogLevel;

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'app_name' => env('APP_NAME', 'nano-demo'),
            'app_env' => env('APP_ENV', 'dev'),
            'scan_cacheable' => env('SCAN_CACHEABLE', false),
            StdoutLoggerInterface::class => [
                'log_level' => [
                    LogLevel::ALERT,
                    LogLevel::CRITICAL,
                    LogLevel::DEBUG,
                    LogLevel::EMERGENCY,
                    LogLevel::ERROR,
                    LogLevel::INFO,
                    LogLevel::NOTICE,
                    LogLevel::WARNING,
                ],
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        // BASE_PATH . '/',
                    ],
                ],
            ],
            'aspects' => [],
            'commands' => [],
            'crontab' => [],
            'databases' => [],
            'dependencies' => [],
            'exceptions' => [
                'handler' => [
                    'http' => [
                        // App\Exception\Handler\BusinessExceptionHandler::class,
                    ],
                ],
            ],
            'listeners' => [
                Hyperf\ExceptionHandler\Listener\ErrorExceptionHandler::class,
                Hyperf\Command\Listener\FailToHandleListener::class,
            ],
            'logger' => [],
            'middlewares' => [
                'http' => [],
            ],
            'processes' => [],
            'redis' => [],
            'server' => [
                'settings' => [
                    'daemonize' => (int) env('DAEMONIZE', 0),
                ],
            ],
        ];
    }
}

Closure::bind(function () {
    self::getLockContent();
    self::$extra[uniqid()] = [
        'hyperf' => [
            'config' => ConfigProvider::class,
        ],
    ];
    ClassLoader::init();
}, null, Composer::class)();

$app = AppFactory::createBase('0.0.0.0', 9501);

$app->get('/', function () {
    return 'Hello Nano!';
});

$app->addCommand('nano', function () {
    $this->info('Hello, Nano!');
});

$app->run();
