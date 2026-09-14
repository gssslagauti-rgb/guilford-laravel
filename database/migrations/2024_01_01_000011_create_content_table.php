<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create("content", function (Blueprint $t) { $t->id(); $t->foreignId("section_id")->constrained("sections")->cascadeOnDelete(); $t->string("title"); $t->text("body")->nullable(); $t->text("metadata_json")->nullable(); $t->integer("contributor_id")->nullable(); $t->string("contributor_name")->nullable(); $t->boolean("published")->default(true); $t->timestamps(); }); }
    public function down(): void { Schema::dropIfExists("content"); }
};