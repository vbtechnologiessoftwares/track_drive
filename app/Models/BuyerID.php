<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerID extends Model
{
    use HasFactory;
    protected $primaryKey = 'primary_id';
    protected $table = 'buyerids';
    
    // Specify custom column names
    const CREATED_AT = 'primary_created_at';
    const UPDATED_AT = 'primary_updated_at';

    protected $fillable = [
						    'id',
						    'buyer_id',
						    'name',
						
						];
}
