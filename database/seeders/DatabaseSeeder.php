<?php
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
}