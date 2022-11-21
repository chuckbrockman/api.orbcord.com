<?php

namespace App\Models;

use App\Services\Helpers\Location;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class PageSpeedAudit extends Model
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
    protected $casts = [
        'meta_data' => 'array',
        'data_raw' => 'array',
        'data_normalized' => 'array',
        'referrer' => 'array',
    ];


    /**
     * Make Audit Source upper case
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function auditSource(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }

    /**
     * Make Audit Type upper case
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function auditType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }

    /**
     * Make Device Type upper case
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function deviceType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }

    /**
     * Make Device Type upper case
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function ipAddress(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Location::longToIpv4($value),
            set: fn ($value) => Location::ipv4ToLong($value),
        );
    }


    /**
     * Pagespeed Audit Sources
     */
    public function pagespeedAuditSource()
    {
        return $this->belongsTo(PageSpeedAuditSource::class);
    }


    /**
     * Pagespeed Audit Sources
     */
    public function webhookData()
    {
        return $this->belongsToMany(WebhookData::class);
    }

}
