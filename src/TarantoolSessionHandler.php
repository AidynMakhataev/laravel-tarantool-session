<?php

declare(strict_types=1);

namespace AidynMakhataev\Tarantool\Session;

use Tarantool\Client\Client;
use Tarantool\Client\Schema\Criteria;

/**
 * Class TarantoolSessionHandler
 * @package AidynMakhataev\Tarantool\Session
 */
final class TarantoolSessionHandler implements \SessionHandlerInterface
{
    /** @var Client  */
    private $client;

    /** @var \Tarantool\Client\Schema\Space  */
    private $space;

    /** @var string */
    private $sessionKeyPrefix;

    /** @var string  */
    private $gcFunctionName = 'php_sessions.gc';

    public function __construct(string $host, string $user, string $password, string $space)
    {
        $this->client = Client::fromOptions([
            'uri'       =>  $host,
            'username'  =>  $user,
            'password'  =>  $password
        ]);

        $this->space = $this->client->getSpace($space);

        session_set_save_handler($this);
    }

    public function close(): bool
    {
        $this->sessionKeyPrefix = null;

        return true;
    }

    public function destroy($session_id): bool
    {
        $this->space->delete([$this->makeKey($session_id)]);

        return true;
    }

    public function gc($maxlifetime): bool
    {
        $this->client->call($this->gcFunctionName, $this->space, $maxlifetime);

        return true;
    }

    public function open($save_path, $name): bool
    {
        $this->sessionKeyPrefix = $save_path . '^' . $name;

        return true;
    }

    public function read($session_id)
    {
        $data = $this->space->select(Criteria::key([$this->makeKey($session_id)]));

        return count($data) === 1 ? $data[0][1] : '';
    }

    public function write($session_id, $session_data): bool
    {
        $this->space->replace([$this->makeKey($session_id), $session_data, time()]);

        return true;
    }

    /**
     * @param $session_id
     *
     * @return string
     */
    private function makeKey($session_id): string
    {
        return $this->sessionKeyPrefix.'@'.$session_id;
    }
}