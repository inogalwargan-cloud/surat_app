<?php

namespace Tests\Feature;

use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LetterTemplateTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
    }

    public function test_authenticated_user_can_view_template_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('template.index'));
        $response->assertStatus(200);
        $response->assertSee('Template Surat');
    }

    public function test_authenticated_user_can_view_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('template.create', ['type' => 'undangan']));
        $response->assertStatus(200);
        $response->assertSee('Surat Undangan');
    }

    public function test_user_can_store_new_letter_template(): void
    {
        $payload = [
            'title' => 'Test Surat Permohonan Bantuan',
            'type' => 'permohonan',
            'reference_number' => 'TEST/001/X/2026',
            'regarding' => 'Permohonan Bantuan',
            'attachment' => '1 Berkas',
            'letter_date' => '2026-10-20',
            'letter_place' => 'Jakarta',
            'to_name' => 'Bapak Kepala Kantor',
            'to_address' => 'di Jakarta',
            'opening_salutation' => 'Dengan hormat,',
            'opening_text' => 'Bersama surat ini kami mengajukan permohonan.',
            'content' => 'Rincian permohonan bantuan...',
            'closing_text' => 'Demikian disampaikan.',
            'closing_salutation' => 'Hormat kami,',
            'signer_position' => 'Ketua Pelaksana',
            'signer_name' => 'Budi Santoso',
            'signer_identity_number' => '198501012010011001',
        ];

        $response = $this->actingAs($this->user)->post(route('template.store'), $payload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('letter_templates', [
            'reference_number' => 'TEST/001/X/2026',
            'type' => 'permohonan',
        ]);
    }

    public function test_user_can_download_template_as_word(): void
    {
        $template = LetterTemplate::where('type', 'undangan')->first();
        $response = $this->actingAs($this->user)->get(route('template.download', $template));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }

    public function test_user_can_duplicate_template(): void
    {
        $template = LetterTemplate::first();
        $response = $this->actingAs($this->user)->post(route('template.duplicate', $template));

        $response->assertStatus(302);
        $this->assertDatabaseHas('letter_templates', [
            'title' => $template->title . ' (Salinan)',
        ]);
    }
}
