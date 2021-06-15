<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitialTableSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('cooking_recipes', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('tags')->nullable();
			$table->integer('serving_size')->nullable();
			$table->integer('prep_time')->nullable();
			$table->integer('cook_time')->nullable();
			$table->integer('created_by')->references('id')->on('persons');
			$table->integer('updated_by')->references('id')->on('persons');
			$table->boolean('private')->default(true);
			$table->timestamps();
		});

		Schema::create('cooking_recipe_components', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->integer('linked_recipe_id')->references('id')->on('cooking_recipes');
			$table->timestamps();
		});

		Schema::create('cooking_recipe_steps', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('cooking_recipe_component_id')->references('id')->on('cooking_recipe_components');
			$table->integer('order');
			$table->string('name');
			$table->text('description');
			$table->integer('expected_minutes')->nullable();
			$table->integer('linked_recipe_id')->references('id')->on('cooking_recipes')->nullable();
			$table->integer('linked_component_id')->references('id')->on('cooking_recipe_components')->nullable();
			$table->timestamps();
		});

		Schema::create('food_items', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('tags')->nullable();
			$table->timestamps();
		});

		Schema::create('measurement_scales', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('abbr');
			$table->boolean('solid')->default(true);
			$table->boolean('liquid')->default(false);
			$table->timestamps();
		});

		Schema::create('food_item_nutritions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('food_item_id')->references('id')->on('food_items');
			$table->float('amount');
			$table->string('measurement_scale_id')->references('id')->on('measurement_scales');
			$table->integer('calories');
			$table->integer('carbohydrates');
			$table->integer('sodium');
			$table->integer('fat');
			$table->integer('protein');
			$table->integer('sugar');
			$table->timestamps();
		});

		Schema::create('cooking_recipe_nutritions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('cooking_recipe_component_id')->references('id')->on('cooking_recipe_components');
			$table->float('calories');
			$table->float('carbohydrates');
			$table->float('sodium');
			$table->float('fat');
			$table->float('protein');
			$table->float('sugar');
			$table->timestamps();
		});		

		Schema::create('cooking_recipe_ingredients', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('cooking_recipe_component_id')->references('id')->on('cooking_recipe_components');
			$table->float('amount');
			$table->integer('measurement_scale_id')->references('id')->on('measurement_scales');
			$table->integer('food_item_id')->references('id')->on('food_items');
			$table->integer('substitute')->references('id')->on('cooking_recipe_ingredients')->nullable();
			$table->timestamps();
		});

		Schema::create('persons', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('first_name');
			$table->string('last_name');
			$table->integer('user_id')->references('id')->on('users');
			$table->timestamps();
		});		

		Schema::create('groups', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name')->nullable();
			$table->integer('creator')->references('id')->on('persons');
			$table->timestamps();
		});

		Schema::create('group_roles', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->integer('authority_level');
			$table->boolean('can_invite')->default(false);
			$table->boolean('can_remove')->default(false);
			$table->boolean('can_promote')->default(false);
			$table->boolean('can_assign')->default(false);
			$table->timestamps();
		});

		Schema::create('persons_to_groups', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->integer('group_id')->references('id')->on('groups');
			$table->integer('group_role_id')->references('id')->on('group_roles');
			$table->timestamps();
		});

		Schema::create('pantries', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('group_id')->references('id')->on('groups');
			$table->boolean('autostock')->default(false);
			$table->timestamps();
		});

		Schema::create('pantry_food_items', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('pantry_id')->references('id')->on('pantries');
			$table->integer('food_item_id')->references('id')->on('food_items');
			$table->integer('quantity');
			$table->integer('expires_on')->default(0);
			$table->float('size')->nullable();
			$table->integer('measurement_scale_id')->references('id')->on('measurement_scales')->nullable();
			$table->timestamps();
			$table->unique(['pantry_id', 'food_item_id', 'expires_on']);
		});

		Schema::create('shopping_lists', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons')->nullable();
			$table->integer('group_id')->references('id')->on('groups')->nullable();
			$table->string('name');
			$table->boolean('autostock')->default(false);
			$table->timestamp('created_on');
			$table->timestamp('shopped_on')->nullable();
			$table->integer('shopped_by')->references('id')->on('persons')->nullable();
			$table->boolean('completed')->default(false);
			$table->boolean('private')->default(false);
			$table->timestamps();
		});

		Schema::create('shopping_list_food_items', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('shopping_list_id')->references('id')->on('shopping_lists');
			$table->integer('food_item_id')->references('id')->on('food_items');
			$table->integer('quantity');
			$table->boolean('received')->default(false);
			$table->integer('quantity_received')->nullable();
			$table->timestamps();
		});

		Schema::create('tasks', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->integer('group_id')->references('id')->on('groups')->nullable();
			$table->integer('task_list_id')->references('id')->on('task_lists')->nullable();
			$table->string('label');
			$table->text('info');
			$table->boolean('repeat_weekly')->default(false);
			$table->integer('repeat_dow')->nullable();
			$table->boolean('repeat_monthly')->default(false);
			$table->integer('repeat_day')->nullable();
			$table->date('target_date')->nullable();
			$table->boolean('is_reminder')->default(false);
			$table->timestamps();
		});

		Schema::create('task_lists', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->string('name');
			$table->timestamps();
		});

		Schema::create('tasks_history', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('task_id')->references('id')->on('tasks');
			$table->integer('completed_by')->references('id')->on('persons');
			$table->timestamps();
		});

		Schema::create('exercises', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->integer('exercise_type_id')->references('id')->on('exercise_types');
			$table->string('name');
			$table->string('tags')->nullable();
			$table->float('duration')->nullable();
			$table->float('sets')->nullable();
			$table->float('reps')->nullable();
			$table->float('distance')->nullable();
			$table->timestamp('date_recorded');
			$table->timestamps();
		});

		Schema::create('exercise_types', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->string('name');
			$table->string('labels');
			$table->text('description')->nullable();
			$table->boolean('is_duration')->default(false);
			$table->boolean('is_set')->default(false);
			$table->float('calories_per_hour')->nullable();
			$table->float('calories_per_mile')->nullable();
			$table->timestamps();
		});

		Schema::create('exercise_goals', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->float('target_duration')->nullable();
			$table->float('target_sets')->nullable();
			$table->float('target_reps')->nullable();
			$table->float('target_distance')->nullable();
			$table->integer('target_exercise')->references('id')->on('exercises')->nullable();
			$table->integer('target_exercise_type')->references('id')->on('exercise_types')->nullable();
			$table->boolean('is_daily_goal')->default(false);
			$table->boolean('is_weekly_goal')->default(true);
			$table->boolean('is_monthly_goal')->default(false);
			$table->timestamps();
		});

		Schema::create('fitness_history', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->boolean('is_current')->default(true);
			$table->integer('weight');
			$table->float('body_fat');
			$table->timestamps();
		});

		Schema::create('nutrition_intakes', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->integer('food_item_id')->references('id')->on('food_items');
			$table->integer('amount');
			$table->integer('measurement_scale_id')->references('id')->on('measurement_scales');
			$table->timestamps();
		});

		Schema::create('diet_goals', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('person_id')->references('id')->on('persons');
			$table->boolean('is_daily')->default(true);
			$table->boolean('is_weekly')->default(false);
			$table->boolean('is_monthly')->default(false);
			$table->float('calories');
			$table->float('carbohydrates');
			$table->float('sodium');
			$table->float('fat');
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::dropIfExists('cooking_recipes');
		Schema::dropIfExists('cooking_recipe_steps');
		Schema::dropIfExists('cooking_recipe_components');
		Schema::dropIfExists('cooking_recipe_ingredients');
		Schema::dropIfExists('cooking_recipe_nutritions');
		Schema::dropIfExists('diet_goals');
		Schema::dropIfExists('exercise_goals');
		Schema::dropIfExists('exercise_types');
		Schema::dropIfExists('exercises');
		Schema::dropIfExists('fitness_history');
		Schema::dropIfExists('food_item_nutritions');
		Schema::dropIfExists('food_items');
		Schema::dropIfExists('group_roles');
		Schema::dropIfExists('groups');
		Schema::dropIfExists('measurement_scales');
		Schema::dropIfExists('nutrition_intakes');
		Schema::dropIfExists('pantries');
		Schema::dropIfExists('pantry_food_items');
		Schema::dropIfExists('persons');
		Schema::dropIfExists('persons_to_groups');
		Schema::dropIfExists('shopping_list_food_items');
		Schema::dropIfExists('shopping_lists');
		Schema::dropIfExists('task_lists');
		Schema::dropIfExists('tasks');
		Schema::dropIfExists('tasks_history');
    }
}
