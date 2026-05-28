<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UrgentDocumentVote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'urgent_document_id',
        'user_id',
        'vote_category_id',
    ];

    public function urgentDocument()
    {
        return $this->belongsTo(UrgentDocument::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voteCategory()
    {
        return $this->belongsTo(VoteCategory::class);
    }
}
