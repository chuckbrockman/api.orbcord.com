<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSpeedAuditSource extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_data';

    /**
     * Pagespeed Audits
     */
    public function pagespeedAudits()
    {
        return $this->hasMany(PageSpeedAudit::class);
    }

}
