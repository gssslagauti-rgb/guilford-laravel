<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Section extends Model {
    public $timestamps = false;
    protected $fillable = ["slug","title","description","is_meeting","order_index"];
    protected $casts = ["is_meeting"=>"boolean"];
    public function content() { return $this->hasMany(Content::class); }
}