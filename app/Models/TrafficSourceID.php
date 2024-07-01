<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficSourceID extends Model
{
     use HasFactory;
    protected $primaryKey = 'primary_id';
    protected $table = 'trafficsourceids';
    
    // Specify custom column names
    const CREATED_AT = 'primary_created_at';
    const UPDATED_AT = 'primary_updated_at';

    protected $fillable = [
						    'id',
						    'traffic_source_id',
						    'name',
						
						];
}
