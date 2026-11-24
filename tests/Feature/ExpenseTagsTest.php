<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\PaymentType;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTagsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Lunch',
            'amount' => 12.5,
            'transaction_date' => '2026-11-20',
            'currency_id' => Currency::create(['name' => 'Dollar', 'code' => 'USD'])->id,
            'category_id' => Category::create(['name' => 'Food', 'budget' => 0])->id,
            'payment_type_id' => PaymentType::create(['name' => 'Cash'])->id,
        ], $overrides);
    }

    private function makeExpense(array $tags = [], array $overrides = []): Expense
    {
        $expense = Expense::create(array_merge($this->payload(), ['user_id' => $this->user->id], $overrides));
        $expense->syncTagNames($tags, $this->user->id);

        return $expense;
    }

    public function test_expense_can_be_created_with_tags()
    {
        $this->post(route('expenses.store'), $this->payload(['tags' => ['work', 'travel']]))
            ->assertSessionHasNoErrors();

        $expense = Expense::first();
        $this->assertEqualsCanonicalizing(['work', 'travel'], $expense->tags->pluck('name')->all());
        $this->assertSame($this->user->id, $expense->tags->first()->user_id);
    }

    public function test_expense_can_be_created_without_tags()
    {
        $this->post(route('expenses.store'), $this->payload())->assertSessionHasNoErrors();

        $this->assertCount(0, Expense::first()->tags);
    }

    public function test_update_replaces_tags_and_removes_cleared_ones()
    {
        $expense = $this->makeExpense(['work', 'travel']);

        $this->patch(route('expenses.update', $expense->id), $this->payload(['tags' => ['travel', 'food']]))
            ->assertSessionHasNoErrors();

        $this->assertEqualsCanonicalizing(['travel', 'food'], $expense->fresh()->tags->pluck('name')->all());

        $this->patch(route('expenses.update', $expense->id), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertCount(0, $expense->fresh()->tags);
    }

    public function test_tags_are_trimmed_deduplicated_case_insensitively_and_reused()
    {
        $this->makeExpense(['work']);

        $this->post(route('expenses.store'), $this->payload(['tags' => [' Work ', 'work', '', 'new']]));

        $this->assertSame(2, Tag::count());
        $this->assertEqualsCanonicalizing(
            ['work', 'new'],
            Expense::latest('created_at')->get()->first(fn($e) => $e->tags->count() === 2)->tags->pluck('name')->map(fn($n) => strtolower($n))->all()
        );
    }

    public function test_tags_must_be_an_array_of_short_strings()
    {
        $this->post(route('expenses.store'), $this->payload(['tags' => 'work']))->assertSessionHasErrors('tags');
        $this->post(route('expenses.store'), $this->payload(['tags' => [str_repeat('a', 51)]]))->assertSessionHasErrors('tags.0');
    }

    public function test_tag_with_same_name_is_separate_per_user()
    {
        $other = User::factory()->create();
        Tag::create(['user_id' => $other->id, 'name' => 'work']);

        $this->post(route('expenses.store'), $this->payload(['tags' => ['work']]));

        $this->assertSame(2, Tag::where('name', 'work')->count());
        $this->assertSame($this->user->id, Expense::first()->tags->first()->user_id);
    }

    public function test_tags_index_returns_only_own_tag_names_sorted()
    {
        Tag::create(['user_id' => $this->user->id, 'name' => 'zebra']);
        Tag::create(['user_id' => $this->user->id, 'name' => 'apple']);
        Tag::create(['user_id' => User::factory()->create()->id, 'name' => 'secret']);

        $this->getJson(route('tags.index'))->assertOk()->assertExactJson(['apple', 'zebra']);
    }

    public function test_show_includes_tags()
    {
        $expense = $this->makeExpense(['work']);

        $this->getJson(route('expenses.show', $expense->id))
            ->assertOk()
            ->assertJsonPath('tags.0.name', 'work');
    }

    public function test_search_matches_tag_names()
    {
        $tagged = $this->makeExpense(['holiday'], ['name' => 'Hotel']);
        $this->makeExpense([], ['name' => 'Groceries']);

        $this->get(route('expenses.index', ['search' => 'holiday']))
            ->assertOk()
            ->assertInertia(fn($page) => $page
                ->component('Expenses/Index', false)
                ->has('items.data', 1)
                ->where('items.data.0.id', $tagged->id)
                ->where('items.data.0.tags.0.name', 'holiday'));
    }

    public function test_deleting_a_tag_removes_pivot_rows()
    {
        $expense = $this->makeExpense(['work']);

        Tag::first()->delete();

        $this->assertCount(0, $expense->fresh()->tags);
        $this->assertDatabaseCount('expense_tag', 0);
    }
}
