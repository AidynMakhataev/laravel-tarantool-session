<?php

declare(strict_types=1);

namespace AidynMakhataev\Tarantool\Session\Console;

use AidynMakhataev\Tarantool\Session\TarantoolSessionHandler;
use Illuminate\Console\Command;

/**
 * Class TransferSessionFromFileCommand.
 */
final class TransferSessionFromFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tarantool-session:transfer-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer session from file storage to tarantool';

    /** @var TarantoolSessionHandler */
    private $sessionHandler;

    private static $ignoreFileList = [
        '.gitignore',
    ];

    /**
     * Create a new command instance.
     *
     * @param TarantoolSessionHandler $handler
     */
    public function __construct(TarantoolSessionHandler $handler)
    {
        $this->sessionHandler = $handler;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $directory = new \DirectoryIterator(storage_path('framework/sessions'));

        foreach ($directory as $file) {
            if ($file->isFile() && ! in_array($file->getFilename(), self::$ignoreFileList)) {
                $key = $file->getFilename();

                $value = file_get_contents($file->getPathname());

                $this->sessionHandler->write($key, $value);
            }
        }

        $this->info('Transfer successfully completed');
    }
}
