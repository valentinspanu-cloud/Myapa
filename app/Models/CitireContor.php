<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitireContor extends Model
{
    protected $table = 'citiri_contoare';

    protected $fillable = [
        'user_id', 'id_cit', 'id_contor', 'cod_abonat', 'id_client', 'id_locatie',
        'ruta', 'sector', 'luna', 'an', 'serie_contor', 'cod_contor', 'tip_contor',
        'index_vechi', 'index_nou_oracle', 'index_citit',
        'sold_moment', 'foto_path', 'gps_lat', 'gps_lng',
        'status', 'observatii', 'mesaj_oracle',
        'confirmat_de', 'confirmat_la',
    ];

    protected $casts = [
        'confirmat_la' => 'datetime',
        'sold_moment'  => 'decimal:2',
        'gps_lat'      => 'decimal:7',
        'gps_lng'      => 'decimal:7',
    ];

    public function cititor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmat_de');
    }

    public function getConsumAttribute(): ?int
    {
        if ($this->index_citit !== null && $this->index_vechi !== null) {
            return $this->index_citit - $this->index_vechi;
        }
        return null;
    }

    public function getMapsUrlAttribute(): ?string
    {
        if (!$this->gps_lat || !$this->gps_lng) return null;
        return "https://maps.google.com/?q={$this->gps_lat},{$this->gps_lng}";
    }

    public function scopeNou($query)
    {
        return $query->where('status', 'nou');
    }

    public function scopeConfirmat($query)
    {
        return $query->where('status', 'confirmat');
    }

    public function scopePerioad($query, int $luna, int $an)
    {
        return $query->where('luna', $luna)->where('an', $an);
    }
}
