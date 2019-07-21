<?php

return [
  'disks' => [
    'dimages' => [
      'driver' => 'local',
      'root' => storage_path('app/public/dimages'),
      'url' => env('APP_URL').'/dimages'
    ],
  ]
];