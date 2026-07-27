<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementAttachment extends Model
{
    protected $fillable = [
        'announcement_id',
        'file_path',
        'file_type',
        'original_name',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
