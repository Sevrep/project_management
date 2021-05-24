<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardFiles extends Model
{
    use HasFactory;

    protected $primaryKey = 'card_file_id';

    protected $fillable = [
        'card_file_title',
        'card_file_filename'

    ];
}
