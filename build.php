<?php
function w($p, $c) {
    $f = __DIR__ . '/' . $p;
    if (!is_dir(dirname($f))) mkdir(dirname($f), 0775, true);
    file_put_contents($f, $c);
    echo "  + $p\n";
}
echo "Building Guilford Hub...\n\n";

w('app/Models/Section.php', '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Section extends Model {
    public $timestamps = false;
    protected $fillable = ["slug","title","description","is_meeting","order_index"];
    protected $casts = ["is_meeting"=>"boolean"];
    public function content() { return $this->hasMany(Content::class); }
}');

w('app/Models/Content.php', '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Content extends Model {
    protected $table = "content";
    protected $fillable = ["section_id","title","body","metadata_json","contributor_id","contributor_name","published"];
    protected $casts = ["published"=>"boolean"];
    public function section() { return $this->belongsTo(Section::class); }
    public function getMetadataAttribute(): array { return json_decode($this->metadata_json ?? "{}", true) ?: []; }
}');

w('app/Models/Contribution.php', '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Contribution extends Model {
    protected $fillable = ["section_id","user_id","contribution_type","target_id","content_json","status","moderator_id","moderator_note","reviewed_at"];
    public function user() { return $this->belongsTo(User::class); }
    public function section() { return $this->belongsTo(Section::class); }
    public function getContentAttribute(): array { return json_decode($this->content_json ?? "{}", true) ?: []; }
}');

w('app/Models/Subscriber.php', '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Subscriber extends Model {
    public $timestamps = false;
    protected $fillable = ["email"];
}');

w('app/Http/Middleware/AdminMiddleware.php', '<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class AdminMiddleware {
    public function handle(Request $request, Closure $next) {
        if (!auth()->check() || !auth()->user()->isAdmin()) abort(403);
        return $next($request);
    }
}');

w('app/Http/Controllers/HomeController.php', '<?php
namespace App\Http\Controllers;
use App\Models\Content;
use App\Models\Section;
class HomeController extends Controller {
    public function index() {
        $sections = Section::orderBy("order_index")->get();
        $grouped = [];
        foreach ($sections as $s) $grouped[$s->slug] = ["section"=>$s,"items"=>Content::where("section_id",$s->id)->where("published",true)->orderByDesc("updated_at")->limit(12)->get()];
        return view("home", compact("grouped"));
    }
    public function section($slug) {
        $section = Section::where("slug",$slug)->firstOrFail();
        $items = Content::where("section_id",$section->id)->where("published",true)->orderByDesc("updated_at")->get();
        return view("section", compact("section","items"));
    }
}');

w('app/Http/Controllers/AuthController.php', '<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller {
    public function showLogin() { return view("auth.login"); }
    public function login(Request $r) {
        $c = $r->validate(["email"=>"required|email","password"=>"required"]);
        $u = User::where("email",$c["email"])->first();
        if (!$u || $u->is_banned || !Hash::check($c["password"],$u->password)) return back()->withErrors(["email"=>"Invalid credentials."]);
        Auth::login($u);
        $u->update(["last_login"=>now()]);
        if ($u->isAdmin()) return redirect("/admin");
        if ($u->isModerator()) return redirect("/moderator");
        return redirect("/");
    }
    public function showRegister() { return view("auth.register"); }
    public function register(Request $r) {
        $d = $r->validate(["display_name"=>"required|string|max:120","email"=>"required|email|unique:users,email","password"=>"required|min:8"]);
        $u = User::create(["display_name"=>$d["display_name"],"email"=>strtolower($d["email"]),"password"=>Hash::make($d["password"]),"role"=>"user"]);
        Auth::login($u);
        return redirect("/");
    }
    public function logout() { Auth::logout(); return redirect("/"); }
}');

w('app/Http/Controllers/AdminController.php', '<?php
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
}');

w('app/Http/Controllers/ModeratorController.php', '<?php
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
}');

w('app/Http/Controllers/ContributeController.php', '<?php
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
}');

w('routes/web.php', '<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContributeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModeratorController;
use Illuminate\Support\Facades\Route;
Route::get("/", [HomeController::class,"index"])->name("home");
Route::get("/section/{slug}", [HomeController::class,"section"])->name("section");
Route::post("/subscribe", [ContributeController::class,"subscribe"])->name("subscribe");
Route::get("/login", [AuthController::class,"showLogin"])->name("login");
Route::post("/login", [AuthController::class,"login"]);
Route::get("/register", [AuthController::class,"showRegister"])->name("register");
Route::post("/register", [AuthController::class,"register"]);
Route::post("/logout", [AuthController::class,"logout"])->name("logout");
Route::middleware("auth")->post("/contribute", [ContributeController::class,"submit"])->name("contribute");
Route::middleware("auth")->prefix("moderator")->group(function () {
    Route::get("/", [ModeratorController::class,"dashboard"])->name("moderator.dashboard");
    Route::post("/moderate/{contribution}", [ModeratorController::class,"moderate"])->name("moderator.moderate");
});
Route::middleware("auth")->prefix("admin")->group(function () {
    Route::get("/", [AdminController::class,"dashboard"])->name("admin.dashboard");
    Route::get("/users", [AdminController::class,"users"])->name("admin.users");
    Route::post("/users", [AdminController::class,"createUser"])->name("admin.users.create");
    Route::post("/users/{user}/ban", [AdminController::class,"toggleBan"])->name("admin.users.ban");
    Route::delete("/users/{user}", [AdminController::class,"deleteUser"])->name("admin.users.delete");
    Route::get("/content", [AdminController::class,"content"])->name("admin.content");
    Route::post("/content", [AdminController::class,"saveContent"])->name("admin.content.create");
    Route::delete("/content/{content}", [AdminController::class,"deleteContent"])->name("admin.content.delete");
    Route::get("/sections", [AdminController::class,"sections"])->name("admin.sections");
    Route::get("/subscribers", [AdminController::class,"subscribers"])->name("admin.subscribers");
});
');

w('database/migrations/2024_01_01_000010_create_sections_table.php', '<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create("sections", function (Blueprint $t) { $t->id(); $t->string("slug")->unique(); $t->string("title"); $t->text("description")->nullable(); $t->boolean("is_meeting")->default(false); $t->integer("order_index")->default(0); }); }
    public function down(): void { Schema::dropIfExists("sections"); }
};');

w('database/migrations/2024_01_01_000011_create_content_table.php', '<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create("content", function (Blueprint $t) { $t->id(); $t->foreignId("section_id")->constrained("sections")->cascadeOnDelete(); $t->string("title"); $t->text("body")->nullable(); $t->text("metadata_json")->nullable(); $t->integer("contributor_id")->nullable(); $t->string("contributor_name")->nullable(); $t->boolean("published")->default(true); $t->timestamps(); }); }
    public function down(): void { Schema::dropIfExists("content"); }
};');

w('database/migrations/2024_01_01_000012_create_contributions_table.php', '<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create("contributions", function (Blueprint $t) { $t->id(); $t->foreignId("section_id")->constrained("sections"); $t->foreignId("user_id")->constrained("users"); $t->string("contribution_type")->default("new"); $t->integer("target_id")->nullable(); $t->text("content_json"); $t->string("status")->default("pending"); $t->integer("moderator_id")->nullable(); $t->text("moderator_note")->nullable(); $t->timestamp("reviewed_at")->nullable(); $t->timestamps(); }); }
    public function down(): void { Schema::dropIfExists("contributions"); }
};');

w('database/migrations/2024_01_01_000013_create_subscribers_table.php', '<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create("subscribers", function (Blueprint $t) { $t->id(); $t->string("email")->unique(); $t->timestamp("created_at")->useCurrent(); }); }
    public function down(): void { Schema::dropIfExists("subscribers"); }
};');

w('database/seeders/DatabaseSeeder.php', '<?php
namespace Database\Seeders;
use App\Models\Content;
use App\Models\Section;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        $sections = [
            ["events","Upcoming Events","What is happening in Guilford",0,1],
            ["projects","Ongoing Town Projects","Active municipal projects",0,2],
            ["town-council","Town Council Meeting","Most recent summary",1,3],
            ["board-of-education","Board of Education Meeting","Most recent summary",1,4],
            ["sports","Sports & Registration","Youth and adult sports",0,5],
            ["construction","Construction & Permits","New builds and permits",0,6],
            ["debates","Current Debates","What the town is discussing",0,7],
            ["government","Government Announcements","Official news",0,8],
            ["topics","Hottest Topics","Trending in town",0,9],
            ["recommendations","Recommendations","Restaurants, activities",0,10],
            ["lore","Local Lore","Insider knowledge",0,11],
        ];
        foreach ($sections as $s) Section::firstOrCreate(["slug"=>$s[0]], ["title"=>$s[1],"description"=>$s[2],"is_meeting"=>$s[3],"order_index"=>$s[4]]);
        User::firstOrCreate(["email"=>"admin@guilford.local"], ["display_name"=>"Admin","password"=>Hash::make("admin123"),"role"=>"admin"]);
        User::firstOrCreate(["email"=>"mod@guilford.local"], ["display_name"=>"Maria Moderator","password"=>Hash::make("mod123"),"role"=>"moderator"]);
        User::firstOrCreate(["email"=>"user@guilford.local"], ["display_name"=>"Sam Resident","password"=>Hash::make("user123"),"role"=>"user"]);
        $names = ["Sam","Jane","Bob","Maria","Alex","Chris","Pat","Dana"];
        $titles = ["Guilford Fair","Harvest Festival","Farmers Market","Fall Festival","Town Meeting","Football Tryouts","Fall Ball","Winter Registration","New Cafe Opening","Trail Cleanup","Harbor Walk","Book Fair"];
        $locs = ["Town Green","Fairgrounds","Jacobs Beach","Community Center","Library","Bittner Park","Chaffinch Island"];
        for ($i = 0; $i < 30; $i++) {
            $slug = $sections[rand(0, count($sections)-1)][0];
            $sec = Section::where("slug", $slug)->first();
            if (!$sec) continue;
            $title = $titles[rand(0,count($titles)-1)] . " " . date("Y");
            $meta = ["date" => date("M j, Y", strtotime("+".rand(1,180)." days")), "location" => $locs[rand(0,count($locs)-1)]];
            Content::create(["section_id"=>$sec->id,"title"=>$title,"body"=>"Dynamic content for " . $title,"metadata_json"=>json_encode($meta),"contributor_name"=>$names[rand(0,count($names)-1)],"published"=>true]);
        }
        for ($i = 1; $i <= 15; $i++) Subscriber::firstOrCreate(["email"=>"resident$i@example.com"]);
    }
}');

w('resources/views/layouts/header.blade.php', '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>@yield("title","Guilford Hub")</title><link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset("css/style.css") }}"></head><body><header class="site-header"><div class="container header-inner"><a href="{{ url("/") }}" class="logo"><span class="logo-mark">G</span><span class="logo-text">Guilford Hub</span></a><nav class="main-nav"><a href="{{ url("/") }}">Home</a>@foreach(\App\Models\Section::orderBy("order_index")->take(6)->get() as $nav)<a href="{{ route("section",$nav->slug) }}">{{ $nav->title }}</a>@endforeach</nav><div class="auth-controls">@auth<span class="user-badge">👤 {{ auth()->user()->display_name }}</span>@if(auth()->user()->isModerator())<a href="{{ url("/moderator") }}" class="btn-ghost">Moderate</a>@endif@if(auth()->user()->isAdmin())<a href="{{ url("/admin") }}" class="btn-ghost">Admin</a>@endif<form method="post" action="{{ route("logout") }}" style="display:inline">@csrf<button class="btn-ghost">Log out</button></form>@else<a href="{{ route("login") }}" class="btn-ghost">Sign In</a><a href="{{ route("register") }}" class="btn-primary">Join</a>@endauth</div></div></header>@if(session("success"))<div class="flash">{{ session("success") }}</div>@endif@if($errors->any())<div class="flash err">{{ $errors->first() }}</div>@endif<main>');

w('resources/views/layouts/footer.blade.php', '</main><section class="subscribe-banner"><div class="container"><h3>Stay in the loop</h3><p>Get weekly Guilford updates delivered to your inbox.</p><form action="{{ route("subscribe") }}" method="post" class="subscribe-form">@csrf<input type="email" name="email" placeholder="you@example.com" required><button type="submit" class="btn-primary">Subscribe</button></form></div></section><footer class="site-footer"><div class="container">Guilford Hub</div></footer><script src="{{ asset("js/app.js") }}"></script></body></html>');

w('resources/views/home.blade.php', '@extends("layouts.header")@section("title","Home")<section class="hero"><div class="container"><h1>Guilford, Connecticut</h1><p class="hero-subtitle">Founded 1639 · Long Island Sound · One town, everything connected</p></div></section><div class="container">@foreach($grouped as $slug => $data)@if(in_array($slug,["town-council","board-of-education"]))@continue@endif@if($data["items"]->isEmpty())@continue@endif<section class="section"><div class="section-header"><h2>{{ $data["section"]->title }}</h2><a href="{{ route("section",$slug) }}" class="btn-ghost">View all →</a></div><div class="card-grid">@foreach($data["items"] as $item)@php $m = $item->metadata; @endphp<div class="card"><h3>{{ $item->title }}</h3>@if(!empty($m["date"])||!empty($m["location"]))<div class="card-meta">{{ $m["date"] ?? "" }}{{ !empty($m["location"]) ? " · ".$m["location"] : "" }}</div>@endif@if($item->body)<p>{{ $item->body }}</p>@endif@if($item->contributor_name)<div class="card-meta" style="margin-top:8px">Added by {{ $item->contributor_name }}</div>@endif</div>@endforeach</div></section>@endforeach</div>@include("layouts.footer")');

w('resources/views/section.blade.php', '@extends("layouts.header")@section("title",$section->title)<section class="hero"><div class="container"><h1>{{ $section->title }}</h1><p class="hero-subtitle">{{ $section->description }}</p></div></section><div class="container"><section class="section"><div class="section-header"><h2>{{ $items->count() }} items</h2></div>@if($items->isEmpty())<p>No content yet.</p>@else<div class="card-grid">@foreach($items as $item)<div class="card"><h3>{{ $item->title }}</h3>@if($item->body)<p>{{ $item->body }}</p>@endif</div>@endforeach</div>@endif</section></div>@include("layouts.footer")');

w('resources/views/auth/login.blade.php', '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Sign In</title><link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset("css/style.css") }}"></head><body><div class="auth-box"><h1>Sign In</h1><p class="sub">Welcome back.</p>@if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif<form method="post" action="{{ route("login") }}">@csrf<label>Email</label><input type="email" name="email" required autofocus><label>Password</label><input type="password" name="password" required><button class="btn-primary">Sign In</button></form><p class="link">No account? <a href="{{ route("register") }}">Register</a></p><div class="demo"><strong>Demo:</strong><br>admin@guilford.local / admin123<br>mod@guilford.local / mod123<br>user@guilford.local / user123</div></div></body></html>');

w('resources/views/auth/register.blade.php', '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Register</title><link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset("css/style.css") }}"></head><body><div class="auth-box"><h1>Create Account</h1><p class="sub">Join the community hub.</p>@if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif<form method="post" action="{{ route("register") }}">@csrf<label>Display Name</label><input type="text" name="display_name" required><label>Email</label><input type="email" name="email" required><label>Password (8+)</label><input type="password" name="password" required minlength="8"><button class="btn-primary">Create Account</button></form><p class="link">Have an account? <a href="{{ route("login") }}">Sign In</a></p></div></body></html>');

w('resources/views/admin/dashboard.blade.php', '@extends("layouts.header")@section("title","Admin")<div class="admin-wrap"><aside class="admin-sidebar"><div class="brand">Guilford Admin</div><nav><a href="{{ url("/admin") }}" class="active">Dashboard</a><a href="{{ url("/admin/users") }}">Users</a><a href="{{ url("/admin/content") }}">Content</a><a href="{{ url("/admin/sections") }}">Sections</a><a href="{{ url("/admin/subscribers") }}">Subscribers</a><a href="{{ url("/moderator") }}">Moderation</a><a href="{{ url("/") }}">← Site</a></nav></aside><div class="admin-main"><h1>Dashboard</h1><div class="stat-grid"><div class="stat"><div class="stat-num">{{ $stats["users"] }}</div><div class="stat-label">Users</div></div><div class="stat"><div class="stat-num">{{ $stats["moderators"] }}</div><div class="stat-label">Moderators</div></div><div class="stat warn"><div class="stat-num">{{ $stats["pending"] }}</div><div class="stat-label">Pending</div></div><div class="stat"><div class="stat-num">{{ $stats["content"] }}</div><div class="stat-label">Content</div></div><div class="stat"><div class="stat-num">{{ $stats["subscribers"] }}</div><div class="stat-label">Subscribers</div></div></div><h2>Recent Content</h2><table class="data-table"><thead><tr><th>Title</th><th>Section</th><th>Updated</th></tr></thead><tbody>@foreach($recentContent as $c)<tr><td><strong>{{ $c->title }}</strong></td><td>{{ $c->section->title ?? "—" }}</td><td>{{ $c->updated_at->diffForHumans() }}</td></tr>@endforeach</tbody></table></div></div>@include("layouts.footer")');

w('resources/views/admin/users.blade.php', '@extends("layouts.header")@section("title","Users")<div class="admin-wrap"><aside class="admin-sidebar"><div class="brand">Guilford Admin</div><nav><a href="{{ url("/admin") }}">Dashboard</a><a href="{{ url("/admin/users") }}" class="active">Users</a><a href="{{ url("/admin/content") }}">Content</a><a href="{{ url("/admin/sections") }}">Sections</a><a href="{{ url("/admin/subscribers") }}">Subscribers</a><a href="{{ url("/moderator") }}">Moderation</a><a href="{{ url("/") }}">← Site</a></nav></aside><div class="admin-main"><h1>Users</h1><h2>Create User</h2><form method="post" action="{{ route("admin.users.create") }}" class="grid-2">@csrf<input name="display_name" placeholder="Display Name" required><input type="email" name="email" placeholder="Email" required><input name="password" placeholder="Password (6+)" required minlength="6"><select name="role"><option value="user">User</option><option value="moderator">Moderator</option><option value="admin">Admin</option></select><button class="btn-primary" style="grid-column:1/-1">Create User</button></form><h2>All Users ({{ $users->count() }})</h2><table class="data-table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>@foreach($users as $u)<tr><td><strong>{{ $u->display_name }}</strong></td><td>{{ $u->email }}</td><td><span class="tag">{{ $u->role }}</span></td><td>@if($u->is_banned)<span class="tag warn">Banned</span>@else<span class="tag ok">Active</span>@endif</td><td>@if($u->id !== auth()->id())<form method="post" action="{{ route("admin.users.ban",$u) }}" style="display:inline">@csrf<button class="btn-sm">{{ $u->is_banned ? "Unban" : "Block" }}</button></form><form method="post" action="{{ route("admin.users.delete",$u) }}" style="display:inline" onsubmit="return confirm(\'Delete?\')">@csrf @method("DELETE")<button class="btn-sm danger">Delete</button></form>@endif</td></tr>@endforeach</tbody></table></div></div>@include("layouts.footer")');

w('resources/views/admin/content.blade.php', '@extends("layouts.header")@section("title","Content")<div class="admin-wrap"><aside class="admin-sidebar"><div class="brand">Guilford Admin</div><nav><a href="{{ url("/admin") }}">Dashboard</a><a href="{{ url("/admin/users") }}">Users</a><a href="{{ url("/admin/content") }}" class="active">Content</a><a href="{{ url("/admin/sections") }}">Sections</a><a href="{{ url("/admin/subscribers") }}">Subscribers</a><a href="{{ url("/moderator") }}">Moderation</a><a href="{{ url("/") }}">← Site</a></nav></aside><div class="admin-main"><h1>Content</h1><form method="post" action="{{ route("admin.content.create") }}">@csrf<select name="section_id" required>@foreach($sections as $s)<option value="{{ $s->id }}">{{ $s->title }}</option>@endforeach</select><input name="title" placeholder="Title" required><textarea name="body" placeholder="Body" rows="3"></textarea><button class="btn-primary">Create</button></form><h2>All Content ({{ $items->count() }})</h2><table class="data-table"><thead><tr><th>Title</th><th>Section</th><th>Actions</th></tr></thead><tbody>@foreach($items as $c)<tr><td><strong>{{ $c->title }}</strong></td><td>{{ $c->section->title ?? "—" }}</td><td><form method="post" action="{{ route("admin.content.delete",$c) }}" style="display:inline" onsubmit="return confirm(\'Delete?\')">@csrf @method("DELETE")<button class="btn-sm danger">Delete</button></form></td></tr>@endforeach</tbody></table></div></div>@include("layouts.footer")');

w('resources/views/admin/sections.blade.php', '@extends("layouts.header")@section("title","Sections")<div class="admin-wrap"><aside class="admin-sidebar"><div class="brand">Guilford Admin</div><nav><a href="{{ url("/admin") }}">Dashboard</a><a href="{{ url("/admin/users") }}">Users</a><a href="{{ url("/admin/content") }}">Content</a><a href="{{ url("/admin/sections") }}" class="active">Sections</a><a href="{{ url("/admin/subscribers") }}">Subscribers</a><a href="{{ url("/moderator") }}">Moderation</a><a href="{{ url("/") }}">← Site</a></nav></aside><div class="admin-main"><h1>Sections ({{ $sections->count() }})</h1><table class="data-table"><thead><tr><th>Slug</th><th>Title</th><th>Type</th><th>Order</th></tr></thead><tbody>@foreach($sections as $s)<tr><td><code>{{ $s->slug }}</code></td><td><strong>{{ $s->title }}</strong></td><td>{{ $s->is_meeting ? "Meeting" : "Standard" }}</td><td>{{ $s->order_index }}</td></tr>@endforeach</tbody></table></div></div>@include("layouts.footer")');

w('resources/views/admin/subscribers.blade.php', '@extends("layouts.header")@section("title","Subscribers")<div class="admin-wrap"><aside class="admin-sidebar"><div class="brand">Guilford Admin</div><nav><a href="{{ url("/admin") }}">Dashboard</a><a href="{{ url("/admin/users") }}">Users</a><a href="{{ url("/admin/content") }}">Content</a><a href="{{ url("/admin/sections") }}">Sections</a><a href="{{ url("/admin/subscribers") }}" class="active">Subscribers</a><a href="{{ url("/moderator") }}">Moderation</a><a href="{{ url("/") }}">← Site</a></nav></aside><div class="admin-main"><h1>Subscribers ({{ $subscribers->count() }})</h1><table class="data-table"><thead><tr><th>#</th><th>Email</th></tr></thead><tbody>@foreach($subscribers as $i => $s)<tr><td>{{ $i+1 }}</td><td>{{ $s->email }}</td></tr>@endforeach</tbody></table></div></div>@include("layouts.footer")');

w('resources/views/moderator/dashboard.blade.php', '@extends("layouts.header")@section("title","Moderator")<div class="admin-wrap"><aside class="admin-sidebar"><div class="brand">Moderator</div><nav><a href="{{ url("/moderator") }}" class="active">Pending</a>@if(auth()->user()->isAdmin())<a href="{{ url("/admin") }}">Admin</a>@endif<a href="{{ url("/") }}">← Site</a></nav></aside><div class="admin-main"><h1>Pending ({{ $pending->count() }})</h1>@if($pending->isEmpty())<p>All caught up.</p>@else @foreach($pending as $p)<div class="mod-card"><div class="mod-head"><span class="tag">{{ $p->section->slug }}</span></div><pre>{{ json_encode($p->content, JSON_PRETTY_PRINT) }}</pre><form method="post" action="{{ route("moderator.moderate",$p) }}">@csrf<input name="note" placeholder="Note"><button name="action" value="approved" class="btn-sm ok">Approve</button><button name="action" value="rejected" class="btn-sm danger">Reject</button></form></div>@endforeach @endif</div></div>@include("layouts.footer")');

echo "\nAll done.\n";