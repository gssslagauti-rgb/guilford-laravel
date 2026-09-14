<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Content extends Model {
    protected $table = "content";
    protected $fillable = ["section_id","title","body","metadata_json","contributor_id","contributor_name","published"];
    protected $casts = ["published"=>"boolean"];
    public function section() { return $this->belongsTo(Section::class); }
    public function getMetadataAttribute(): array { return json_decode($this->metadata_json ?? "{}", true) ?: []; }
}