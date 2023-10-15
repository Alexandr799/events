<?php

namespace App\Models;

use App\Events\EventToCacheSave;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'place',
        'date',
        'period',
        'period_type'
    ];

    public static function saveToCache(array $values): string
    {
        $id = uniqid();
        EventToCacheSave::dispatch(new static($values), $id);
        return $id;
    }

    public function toText():string
    {
        $data = $this->title . ' ' . $this->place . ' ' . $this->date;
        if ($this->period > 0) {
            $data .= ' ' . 'через '  . $this->period . ' ' . $this->period_type;
        } else {
            $data .= ' ' . 'было '  . $this->period . ' ' . $this->period_type;
        }
        return $data;
    }

    public static function getFromCache(string $id): Event|false
    {
        $event = Cache::get($id, false);
        if ($event !== false) $event = new static($event);
        return $event;
    }
}
