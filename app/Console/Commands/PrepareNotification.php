<?php

namespace App\Console\Commands;

use App\Models\FeatureFlag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PrepareNotification extends Command
{
    protected $signature = 'app:notification';

    public function handle()
    {
        Artisan::call(FetchData::class);
        Artisan::call(ProcessFlags::class);

        $flags = FeatureFlag::where('status', 'ACTIVE')->orderBy('created_at')->get();

        $groupedByAuthor = [];
        foreach ($flags as $flag) {
            $groupedByAuthor[$flag->author][] = $flag;
        }

        foreach ($groupedByAuthor as $author => $flags) {
            $this->line($author.' ('.count($flags).')');
            foreach ($flags as $flag) {
                $this->line("**$flag->name** (created ".$flag->created_at->diffForHumans().')');
            }
            $this->line('');
        }
    }
}
