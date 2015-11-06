<?php 

$events = array();
foreach(App\Content::with('event')->get() as $row) {
    if (!$row->event) continue;
    $events[] = array(
        'id' => $row->id,
        'title' => $row->title,
        'url' => url($row->seo_slug),
        'class' => 'event-info',
        'start' => strtotime($row->event->start) . '000',
        'end' => strtotime($row->event->end) .'000'
    );
}

return [
    'json' => json_encode($events)
];