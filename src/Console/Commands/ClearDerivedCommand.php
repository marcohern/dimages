<?php


namespace Marcohern\Dimages\Console\Commands;

use Marcohern\Dimages\Lib\Managers\StorageManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearDerivedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dimage:clearderived';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all derived images.';

    protected $sm;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StorageManager $sm)
    {
      parent::__construct();
      $this->sm = $sm;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $cnt = 0;
      $disk = Storage::disk('dimages');
      echo "deleting...\n";
      $tenants = $this->sm->tenants();
      foreach ($tenants as $tenant) {
        $entities = $this->sm->entities($tenant);
        foreach ($entities as $entity) {
          if ($entity != '_sequence') {
            $identities = $this->sm->identities($tenant, $entity);
            foreach ($identities as $identity) {
              $indexes = $this->sm->indexes($tenant, $entity, $identity);
              foreach ($indexes as $indexdir)
              if ($disk->exists($indexdir)) {
                echo "..$indexdir\n";
                $cnt++;
                $disk->deleteDirectory($indexdir);
              }
            }
          }
        }
      }
      echo "deleted $cnt folders.\n";
    }
}
