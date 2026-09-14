<?php
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
}