<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:setup')]
class SetupApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install or update the application, and run migrations after a new release.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('I\'m installing or updating the application.');
        $this->migrate();
        $this->seed();
    }

    /**
     * Run migrations.
     */
    protected function migrate(): void
    {
        $this->artisan('✓ Performing migrations', 'migrate', ['--force' => true]);
    }

    /**
     * Run seeders
     */
    protected function seed(): void
    {
        $this->artisan('✓ Seeding database', 'db:seed', ['--force' => true]);
    }

    private function artisan(string $message, string $command, array $options = []): void
    {
        $this->info($message);
        $this->getOutput()->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE
            ? $this->call($command, $options)
            : $this->callSilent($command, $options);
    }
}
