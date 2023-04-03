<?php

declare(strict_types=1);
/**
 * This file is part of nano-demo.
 *
 * @link     https://github.com/huangdijia/nano-demo
 * @contact  Huangdijia <huangdijia@gmail.com>
 */
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Nano\Factory\AppFactory;
use Hyperf\Server\Event;
use Hyperf\Server\Server;
use Hyperf\WebSocketServer\Constant\Opcode;

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

$app = AppFactory::createBase('0.0.0.0', 9501);

$app->config(['server.servers.1' => [
    'name' => 'ws',
    'type' => Server::SERVER_WEBSOCKET,
    'host' => '0.0.0.0',
    'port' => 9502,
    'sock_type' => SWOOLE_SOCK_TCP,
    'callbacks' => [
        Event::ON_HAND_SHAKE => [\Hyperf\WebSocketServer\Server::class, 'onHandShake'],
        Event::ON_MESSAGE => [\Hyperf\WebSocketServer\Server::class, 'onMessage'],
        Event::ON_CLOSE => [\Hyperf\WebSocketServer\Server::class, 'onClose'],
    ],
],
]);

class WebSocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    public function onMessage($server, $frame): void
    {
        if ($frame->opcode == Opcode::PING) {
            // 如果使用协程 Server，在判断是 PING 帧后，需要手动处理，返回 PONG 帧。
            // 异步风格 Server，可以直接通过 Swoole 配置处理，详情请见 https://wiki.swoole.com/#/websocket_server?id=open_websocket_ping_frame
            $server->push('', Opcode::PONG);
            return;
        }
        $server->push($frame->fd, 'Recv: ' . $frame->data);
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        var_dump('closed');
    }

    public function onOpen($server, $request): void
    {
        $server->push($request->fd, 'Opened');
    }
}

$app->addServer('ws', function () use ($app) {
    $app->get('/', WebSocketController::class);
});

$app->get('/', function () {
    $user = $this->request->input('user', 'nano');
    $method = $this->request->getMethod();
    return [
        'message' => "Hello {$user}",
        'method' => $method,
    ];
});

$app->addCommand('nano', function () {
    $this->info('Hello, Nano!');
});

$app->run();
