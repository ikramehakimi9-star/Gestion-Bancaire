<?php

namespace Tests\Feature;

use App\Models\Compte;
use App\Models\Virement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_formulaire_de_virement_s_affiche(): void
    {
        Compte::factory(2)->create();

        $this->get(route('virements.create'))
            ->assertOk()
            ->assertSee('Nouveau virement');
    }

    public function test_un_virement_valide_met_a_jour_les_deux_soldes(): void
    {
        $emetteur = Compte::factory()->create(['solde' => 1000]);
        $destinataire = Compte::factory()->create(['solde' => 500]);

        $this->post(route('virements.store'), [
            'compte_emetteur_id'     => $emetteur->id,
            'compte_destinataire_id' => $destinataire->id,
            'montant'                => 300,
        ])->assertRedirect(route('virements.index'));

        $this->assertEquals(700, (float) $emetteur->fresh()->solde);
        $this->assertEquals(800, (float) $destinataire->fresh()->solde);
        $this->assertDatabaseHas('virements', [
            'compte_emetteur_id'     => $emetteur->id,
            'compte_destinataire_id' => $destinataire->id,
            'montant'                => 300,
        ]);
    }

    public function test_un_virement_avec_solde_insuffisant_est_refuse(): void
    {
        $emetteur = Compte::factory()->create(['solde' => 100]);
        $destinataire = Compte::factory()->create(['solde' => 500]);

        $this->post(route('virements.store'), [
            'compte_emetteur_id'     => $emetteur->id,
            'compte_destinataire_id' => $destinataire->id,
            'montant'                => 150,
        ])->assertSessionHasErrors('montant');

        // Rien ne doit avoir changé
        $this->assertEquals(100, (float) $emetteur->fresh()->solde);
        $this->assertEquals(500, (float) $destinataire->fresh()->solde);
        $this->assertDatabaseCount('virements', 0);
    }

    public function test_on_peut_virer_exactement_tout_le_solde(): void
    {
        $emetteur = Compte::factory()->create(['solde' => 250]);
        $destinataire = Compte::factory()->create(['solde' => 0]);

        $this->post(route('virements.store'), [
            'compte_emetteur_id'     => $emetteur->id,
            'compte_destinataire_id' => $destinataire->id,
            'montant'                => 250,
        ])->assertRedirect(route('virements.index'));

        $this->assertEquals(0, (float) $emetteur->fresh()->solde);
        $this->assertEquals(250, (float) $destinataire->fresh()->solde);
    }

    public function test_on_ne_peut_pas_virer_vers_le_meme_compte(): void
    {
        $compte = Compte::factory()->create(['solde' => 1000]);

        $this->post(route('virements.store'), [
            'compte_emetteur_id'     => $compte->id,
            'compte_destinataire_id' => $compte->id,
            'montant'                => 100,
        ])->assertSessionHasErrors('compte_destinataire_id');

        $this->assertEquals(1000, (float) $compte->fresh()->solde);
        $this->assertDatabaseCount('virements', 0);
    }

    public function test_le_montant_doit_etre_positif(): void
    {
        $emetteur = Compte::factory()->create(['solde' => 1000]);
        $destinataire = Compte::factory()->create(['solde' => 1000]);

        foreach ([0, -50, 'abc'] as $montant) {
            $this->post(route('virements.store'), [
                'compte_emetteur_id'     => $emetteur->id,
                'compte_destinataire_id' => $destinataire->id,
                'montant'                => $montant,
            ])->assertSessionHasErrors('montant');
        }

        $this->assertDatabaseCount('virements', 0);
    }

    public function test_les_champs_obligatoires_sont_valides(): void
    {
        $this->post(route('virements.store'), [])
            ->assertSessionHasErrors([
                'compte_emetteur_id',
                'compte_destinataire_id',
                'montant',
            ]);
    }

    public function test_le_solde_total_reste_identique_apres_un_virement(): void
    {
        $emetteur = Compte::factory()->create(['solde' => 1000]);
        $destinataire = Compte::factory()->create(['solde' => 500]);
        $totalAvant = (float) Compte::sum('solde');

        $this->post(route('virements.store'), [
            'compte_emetteur_id'     => $emetteur->id,
            'compte_destinataire_id' => $destinataire->id,
            'montant'                => 123.45,
        ]);

        $this->assertEquals($totalAvant, (float) Compte::sum('solde'));
    }

    public function test_une_erreur_technique_annule_toute_la_transaction(): void
    {
        $emetteur = Compte::factory()->create(['solde' => 1000]);
        $destinataire = Compte::factory()->create(['solde' => 500]);

        // On simule une panne au moment d'enregistrer le virement,
        // APRES que les soldes ont déjà été modifiés.
        Virement::creating(function () {
            throw new \RuntimeException('Panne simulée');
        });

        $this->post(route('virements.store'), [
            'compte_emetteur_id'     => $emetteur->id,
            'compte_destinataire_id' => $destinataire->id,
            'montant'                => 300,
        ])->assertSessionHasErrors('montant');

        // Le rollback doit avoir remis les soldes d'origine
        $this->assertEquals(1000, (float) $emetteur->fresh()->solde);
        $this->assertEquals(500, (float) $destinataire->fresh()->solde);
        $this->assertDatabaseCount('virements', 0);

        Virement::flushEventListeners();
    }

    public function test_l_historique_affiche_les_virements(): void
    {
        $emetteur = Compte::factory()->create(['solde' => 1000]);
        $destinataire = Compte::factory()->create(['solde' => 0]);

        $this->post(route('virements.store'), [
            'compte_emetteur_id'     => $emetteur->id,
            'compte_destinataire_id' => $destinataire->id,
            'montant'                => 200,
        ]);

        $this->get(route('virements.index'))
            ->assertOk()
            ->assertSee($emetteur->rib_formate)
            ->assertSee($destinataire->rib_formate);
    }
}
