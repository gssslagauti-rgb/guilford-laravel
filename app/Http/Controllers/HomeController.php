<?php
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
}