<?php

namespace Handlers;

use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Handlers\LogManager;
use Hopla\HistoryManagement\Jobs\LogHistoriesJob;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class LogManagerTest extends TenancyTestCase
{
    private LogManager $logManager;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logManager = new LogManager;
        $this->user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    #[Test]
    public function test_it_generates_login_description(): void
    {
        $description = $this->logManager->generateDescription($this->user, null, HistoryActionType::LOGIN);

        $this->assertStringContainsString('Utilisateur John Doe s\'est connecté.e avec succès.', $description);
    }

    #[Test]
    public function test_it_generates_logout_description(): void
    {

        $description = $this->logManager->generateDescription($this->user, null, HistoryActionType::LOGOUT);

        $this->assertStringContainsString('Utilisateur John Doe s\'est déconnecté.e avec succès.', $description);
    }

    #[Test]
    public function test_it_generates_created_link_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-created.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_CREATED);

        $this->assertStringContainsString('Utilisateur John Doe a crée le lien https://example-created.com.', $description);
    }

    #[Test]
    public function test_it_generates_link_update_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-update.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_UPDATE);

        $this->assertStringContainsString('Utilisateur John Doe a modifié le lien https://example-update.com.', $description);
    }

    #[Test]
    public function test_it_generates_link_share_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-share.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_SHARE);

        $this->assertStringContainsString('Utilisateur John Doe a partagé le lien https://example-share.com.', $description);
    }

    /**
     * @return void
     */
    #[Test]
    public function test_it_generates_link_unshare_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-unshare.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_UNSHARE);

        $this->assertStringContainsString('Utilisateur John Doe a arrêté le partage du lien https://example-unshare.com.', $description);
    }

    #[Test]
    public function test_it_generates_link_expired_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-expired.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_EXPIRED);

        $this->assertStringContainsString('Utilisateur John Doe a un lien expiré : https://example-expired.com.', $description);
    }

    #[Test]
    public function test_it_generates_link_soft_deleted_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-soft-deleted.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_SOFT_DELETE);

        $this->assertStringContainsString('Utilisateur John Doe a déplacé le lien https://example-soft-deleted.com à la Corbeille.', $description);
    }

    #[Test]
    public function test_it_generates_link_restore_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-restore.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_RESTORE);

        $this->assertStringContainsString('Utilisateur John Doe a restoré le lien https://example-restore.com.', $description);
    }

    #[Test]
    public function test_it_generates_link_force_deleted_description(): void
    {
        $link = Link::factory()->create([
            'url' => 'https://example-force-deleted.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $description = $this->logManager->generateDescription($this->user, $link, HistoryActionType::LINK_FORCE_DELETE);

        $this->assertStringContainsString('Utilisateur John Doe a supprimé le lien https://example-force-deleted.com de la Corbeille.', $description);
    }

    #[Test]
    public function it_dispatches_user_history_job(): void
    {
        \Queue::fake();
        $user = User::factory()->create([]);

        $this->logManager->createdUserHistories($this->user, HistoryActionType::LOGIN, 'Test description');

        \Queue::assertPushed(LogHistoriesJob::class, function ($job) {
            return $job->queue === 'logs';
        });
    }

    #[Test]
    public function it_dispatches_link_history_job(): void
    {
        \Queue::fake();
        $user = User::factory()->create([]);
        $link = Link::factory()->create([
            'url' => 'https://example-job.com',
            'expired_at' => now()->addDays(10),
            'user_id' => $this->user->id,
        ]);

        $this->logManager->createdLinkHistories($user, $link, HistoryActionType::LINK_CREATED, 'Test description');

        \Queue::assertPushed(LogHistoriesJob::class, function ($job) {
            return $job->queue === 'logs';
        });
    }
}
