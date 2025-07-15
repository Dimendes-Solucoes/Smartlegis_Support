<?php

namespace App\Services;

use App\Models\Tenancy\Timer;

class TimerService
{
    public function list()
    {
        return Timer::orderBy('title')->get();
    }

    public function find(int $id)
    {
        return Timer::findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $timer = Timer::findOrFail($id);
        $timer->update($data);
    }
}