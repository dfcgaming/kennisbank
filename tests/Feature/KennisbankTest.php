<?php

namespace Tests\Feature;

use App\Enums\AssetType;
use App\Filament\RelationManagers\ArticlesRelationManager;
use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\KnowledgeArticles\Pages\CreateKnowledgeArticle;
use App\Filament\Resources\People\Pages\EditPerson;
use App\Models\Asset;
use App\Models\KnowledgeArticle;
use App\Models\Person;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KennisbankTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_panel_is_only_accessible_to_it_staff(): void
    {
        $this->get('/')->assertRedirect('/it');
        $this->get('/it/assets')->assertRedirect('/it/login');
        $this->get('/it/login')->assertOk()->assertSee('Kennisbank');
        $this->get('/it/register')->assertNotFound();
        $this->actingAs(User::factory()->create())->get('/it/assets')->assertForbidden();

        $this->actingAs(User::factory()->create(['is_it_staff' => true]))
            ->get('/it/assets')->assertOk()->assertSee('Assets');
    }

    public function test_staff_can_assign_and_reclassify_an_asset_and_link_documentation(): void
    {
        $this->actingAs(User::factory()->create(['is_it_staff' => true]));
        Filament::setCurrentPanel(Filament::getPanel('it'));
        $person = Person::create(['name' => 'Jordan Example']);

        Livewire::test(CreateAsset::class)->fillForm([
            'name' => 'Test workstation',
            'type' => AssetType::Laptop->value,
            'asset_tag' => 'IT-001',
            'person_id' => $person->id,
        ])->call('create')->assertHasNoFormErrors();

        $asset = Asset::sole();
        $this->assertTrue($asset->person->is($person));

        Livewire::test(EditAsset::class, ['record' => $asset->getRouteKey()])
            ->fillForm(['type' => AssetType::Server->value])
            ->assertFormFieldIsVisible('hostname')
            ->fillForm(['hostname' => 'internal-test', 'ip_address' => '192.0.2.10'])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame(AssetType::Server, $asset->fresh()->type);
        $this->assertSame('internal-test', $asset->fresh()->hostname);
        $this->assertTrue($asset->fresh()->person->is($person));

        Livewire::test(CreateKnowledgeArticle::class)->fillForm([
            'title' => 'Server handover',
            'body' => 'Check the backup schedule.',
            'assets' => [$asset->id],
            'people' => [$person->id],
        ])->call('create')->assertHasNoFormErrors();

        $article = KnowledgeArticle::sole();
        $this->assertTrue($asset->articles()->first()->is($article));
        $this->assertTrue($person->articles()->first()->is($article));
        $this->get('/it/documentation/'.$article->id)->assertOk()->assertSee('Check the backup schedule.');

        Livewire::test(ArticlesRelationManager::class, ['ownerRecord' => $asset->fresh(), 'pageClass' => EditAsset::class])
            ->assertCanSeeTableRecords([$article]);
        Livewire::test(ArticlesRelationManager::class, ['ownerRecord' => $person, 'pageClass' => EditPerson::class])
            ->assertCanSeeTableRecords([$article]);

        KnowledgeArticle::create(['title' => 'General IT guide', 'body' => 'Contact the helpdesk.']);
        $this->assertSame(1, KnowledgeArticle::doesntHave('assets')->doesntHave('people')->count());
    }
}
