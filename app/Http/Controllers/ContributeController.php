<?php
namespace App\Http\Controllers;
use App\Models\Contribution;
use App\Models\Section;
use App\Models\Subscriber;
use Illuminate\Http\Request;
class ContributeController extends Controller {
    public function submit(Request $r) {
        $r->validate(["section"=>"required","title"=>"required"]);
        $s = Section::where("slug",$r->section)->firstOrFail();
        $data = [];
        foreach (["title","body","date","location","season","registration","category"] as $k) if ($r->filled($k)) $data[$k] = $r->input($k);
        Contribution::create(["section_id"=>$s->id,"user_id"=>auth()->id(),"contribution_type"=>"new","content_json"=>json_encode($data),"status"=>"pending"]);
        return back()->with("success","Awaiting approval.");
    }
    public function subscribe(Request $r) { $r->validate(["email"=>"required|email"]); Subscriber::firstOrCreate(["email"=>strtolower($r->email)]); return back()->with("success","Subscribed!"); }
}