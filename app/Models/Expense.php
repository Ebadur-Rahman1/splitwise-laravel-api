<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['group_id', 'paid_by', 'title', 'amount'];

    public function splits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
