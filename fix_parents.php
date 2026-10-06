<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$activities = \App\Models\Activity::whereNull('parent_activity_id')->where('title', 'LIKE', 'Mendampingi %')->get();
$count = 0;
foreach ($activities as $act) {
    // The title is "Mendampingi {position} dalam kegiatan {title}"
    if (preg_match('/Mendampingi .* dalam kegiatan (.*)/', $act->title, $matches)) {
        $mainTitle = $matches[1];
        // Find the main activity created around the same time
        $parent = \App\Models\Activity::where('title', $mainTitle)
            ->whereNull('parent_activity_id')
            ->whereDate('activity_date', $act->activity_date)
            ->first();
            
        if ($parent) {
            $act->parent_activity_id = $parent->id;
            $act->save();
            $count++;
        }
    }
}
echo "Fixed $count activities.\n";
