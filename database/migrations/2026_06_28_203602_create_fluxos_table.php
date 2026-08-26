<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
	public function up(): void
	{
		Schema::create('fluxos', function (Blueprint $table) {

			$table->id();

			$table->foreignId('cliente_id')
				  ->constrained()
				  ->cascadeOnDelete();

			$table->string('nome');

			$table->text('descricao')->nullable();

			$table->unsignedInteger('versao')->default(1);

			$table->enum('status', [
				'rascunho',
				'aprovacao',
				'aprovado',
				'publicado'
			])->default('rascunho');

			$table->json('json_fluxo')->nullable();

			$table->timestamps();
		});
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fluxos');
    }
};
