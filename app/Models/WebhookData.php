<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookData extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_data';



    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // protected $casts = [
    //     'body' => 'array',
    // ];


    /**
     * Pagespeed Audits
     */
    public function pageSpeedAudits()
    {
        return $this->belongsToMany(PageSpeedAudit::class);
    }
}
