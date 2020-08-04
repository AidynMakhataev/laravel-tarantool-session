<?php

declare(strict_types=1);

namespace AidynMakhataev\Tarantool\Session;

use Tarantool\Client\Client;
use Tarantool\Client\Schema\Criteria;

/**
 * Class TarantoolSessionHandler.
 */
final class TarantoolSessionHandler implements \SessionHandlerInterface
{
    /** @var \Tarantool\Client\Client */
    private $client;

    /** @var string */
    private $spaceName;

    /** @var \Tarantool\Client\Schema\Space */
    private $space;

    /** @var string */
    private $sessionKeyPrefix;

    /** @var string */
    private $gcFunctionName = 'php_sessions.gc';

    public function __construct(Client $client, string $spaceName)
    {
        $this->client = $client;

        $this->spaceName = $spaceName;

        $this->space = $this->client->getSpace($this->spaceName);

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
        $this->client->call($this->gcFunctionName, $this->spaceName, $maxlifetime);

        return true;
    }

    public function open($save_path, $name): bool
    {
        $this->sessionKeyPrefix = $save_path.'^'.$name;

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
