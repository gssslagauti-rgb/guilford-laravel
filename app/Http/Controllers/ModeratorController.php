<?php
namespace App\Http\Controllers;
use App\Models\Content;
use App\Models\Contribution;
use Illuminate\Http\Request;
class ModeratorController extends Controller {
    public function dashboard() { $pending = Contribution::with(["user","section"])->where("status","pending")->orderBy("created_at")->get(); return view("moderator.dashboard", compact("pending")); }
    public function moderate(Request $r, Contribution $c) {
        $r->validate(["action"=>"required|in:approved,rejected"]);
        $c->update(["status"=>$r->action,"moderator_id"=>auth()->id(),"moderator_note"=>$r->note ?? "","reviewed_at"=>now()]);
        if ($r->action === "approved") {
            $data = $c->content;
            Content::create(["section_id"=>$c->section_id,"title"=>$data["title"] ?? "Untitled","body"=>$data["body"] ?? "","metadata_json"=>"{}","contributor_id"=>$c->user_id,"contributor_name"=>$c->user->display_name,"published"=>true]);
        }
        return back()->with("success","Moderated.");
    }
}