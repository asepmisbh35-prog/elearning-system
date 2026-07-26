<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionRevision extends Model
{
    protected $fillable = [
        'assignment_submission_id',
        'score',
        'feedback',
        'graded_by',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
