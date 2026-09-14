<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Contribution extends Model {
    protected $fillable = ["section_id","user_id","contribution_type","target_id","content_json","status","moderator_id","moderator_note","reviewed_at"];
    public function user() { return $this->belongsTo(User::class); }
    public function section() { return $this->belongsTo(Section::class); }
    public function getContentAttribute(): array { return json_decode($this->content_json ?? "{}", true) ?: []; }
}