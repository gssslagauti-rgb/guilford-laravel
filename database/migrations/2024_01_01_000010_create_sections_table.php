<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create("sections", function (Blueprint $t) { $t->id(); $t->string("slug")->unique(); $t->string("title"); $t->text("description")->nullable(); $t->boolean("is_meeting")->default(false); $t->integer("order_index")->default(0); }); }
    public function down(): void { Schema::dropIfExists("sections"); }
};