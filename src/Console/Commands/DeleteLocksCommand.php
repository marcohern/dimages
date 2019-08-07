<?php

namespace Marcohern\Dimages\Console\Commands;

use Illuminate\Console\Command;
use Marcohern\Dimages\Lib\Lockable;

class DeleteLocksCommand extends Command
{
  use Lockable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dimage:clearlocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all source image locks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $this->deleteLocks();
    }
}
