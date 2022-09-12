<?php

namespace App\Console\Commands;

use App\Models\AuditEvent;
use App\Models\FeatureFlag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TopStats extends Command
{
    protected $signature = 'app:stats';

    public function handle()
    {
        Artisan::call(FetchData::class);
        Artisan::call(ProcessFlags::class);

        $this->processTopCreators();
        $this->processTopRemovers();
        $this->processCurrentFlags();
    }

    private function processTopCreators()
    {
        $events = AuditEvent::whereRaw('LOWER(custom_message) LIKE "%created feature flag%"')->get();

        $stats = [];
        foreach ($events as $event) {
            if (! isset($stats[$event['author_name']])) {
                $stats[$event['author_name']] = 0;
            }

            $stats[$event['author_name']] = $stats[$event['author_name']] + 1;
        }

        arsort($stats);

        $data = [];
        foreach ($stats as $author => $count) {
            $data[] = [$author, $count];
        }

        $this->table(
            ['Name', 'Flags Created'],
            $data
        );
    }

    private function processTopRemovers()
    {
        $events = AuditEvent::whereRaw('LOWER(custom_message) LIKE "%deleted feature flag%"')->get();

        $stats = [];
        foreach ($events as $event) {
            if (! isset($stats[$event['author_name']])) {
                $stats[$event['author_name']] = 0;
            }

            $stats[$event['author_name']] = $stats[$event['author_name']] + 1;
        }

        arsort($stats);

        $data = [];
        foreach ($stats as $author => $count) {
            $data[] = [$author, $count];
        }

        $this->table(
            ['Name', 'Flags Deleted'],
            $data
        );
    }

    private function processCurrentFlags()
    {
        $flags = FeatureFlag::where('status', 'ACTIVE')->orderBy('created_at')->get();

        $data = [];
        foreach ($flags as $flag) {
            $data[] = [$flag->name, $flag->author, $flag->created_at->diffForHumans()];
        }

        $this->table(
            ['Name', 'Author', 'Created'],
            $data
        );
    }
}
