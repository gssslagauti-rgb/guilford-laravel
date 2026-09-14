<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create("contributions", function (Blueprint $t) { $t->id(); $t->foreignId("section_id")->constrained("sections"); $t->foreignId("user_id")->constrained("users"); $t->string("contribution_type")->default("new"); $t->integer("target_id")->nullable(); $t->text("content_json"); $t->string("status")->default("pending"); $t->integer("moderator_id")->nullable(); $t->text("moderator_note")->nullable(); $t->timestamp("reviewed_at")->nullable(); $t->timestamps(); }); }
    public function down(): void { Schema::dropIfExists("contributions"); }
};