<?php

return [
  'disks' => [
    'dimages' => [
      'driver' => 'local',
      'root' => storage_path('app/dimages'),
      'url' => env('APP_URL').'/dimages'
    ],
  ]
];