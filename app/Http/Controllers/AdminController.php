<?php
namespace App\Http\Controllers;
use App\Models\Content;
use App\Models\Contribution;
use App\Models\Section;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AdminController extends Controller {
    public function dashboard() {
        $stats = ["users"=>User::count(),"moderators"=>User::where("role","moderator")->count(),"pending"=>Contribution::where("status","pending")->count(),"content"=>Content::where("published",true)->count(),"subscribers"=>Subscriber::count()];
        $recentContent = Content::with("section")->orderByDesc("updated_at")->limit(8)->get();
        return view("admin.dashboard", compact("stats","recentContent"));
    }
    public function users() { $users = User::orderByDesc("created_at")->get(); return view("admin.users", compact("users")); }
    public function createUser(Request $r) {
        $d = $r->validate(["display_name"=>"required","email"=>"required|email|unique:users,email","password"=>"required|min:6","role"=>"required|in:user,moderator,admin"]);
        User::create(["display_name"=>$d["display_name"],"email"=>strtolower($d["email"]),"password"=>Hash::make($d["password"]),"role"=>$d["role"]]);
        return back()->with("success","User created.");
    }
    public function toggleBan(User $user) { if ($user->id===auth()->id()) return back(); $user->update(["is_banned"=>!$user->is_banned]); return back()->with("success","Updated."); }
    public function deleteUser(User $user) { if ($user->id===auth()->id()) return back(); $user->delete(); return back()->with("success","Deleted."); }
    public function resetPassword(Request $r, User $user) { $r->validate(["password"=>"required|min:6"]); $user->update(["password"=>Hash::make($r->password)]); return back()->with("success","Reset."); }
    public function content() { $items = Content::with("section")->orderByDesc("updated_at")->get(); $sections = Section::orderBy("order_index")->get(); return view("admin.content", compact("items","sections")); }
    public function saveContent(Request $r) {
        $d = $r->validate(["section_id"=>"required|exists:sections,id","title"=>"required","body"=>"nullable"]);
        Content::create(["section_id"=>$d["section_id"],"title"=>$d["title"],"body"=>$d["body"] ?? "","metadata_json"=>"{}","contributor_id"=>auth()->id(),"contributor_name"=>auth()->user()->display_name,"published"=>true]);
        return back()->with("success","Created.");
    }
    public function deleteContent(Content $content) { $content->delete(); return back()->with("success","Deleted."); }
    public function sections() { $sections = Section::orderBy("order_index")->get(); return view("admin.sections", compact("sections")); }
    public function subscribers() { $subscribers = Subscriber::orderByDesc("created_at")->get(); return view("admin.subscribers", compact("subscribers")); }
}