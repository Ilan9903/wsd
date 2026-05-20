<?php

namespace Services;

use App\Models\User;
use App\Services\User\UserSocialiteService;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\Datasets\Test\EmptyDataSet;
use Tests\Utils\TenancyTestCase;

class UserSocialiteServiceTest extends TenancyTestCase
{
    #[Test]
    public function test_update_user_fields_if_not_null()
    {

        $user = User::factory()->create([
            'last_name' => 'OldLastName',
            'first_name' => 'OldFirstName',
        ]);

        $fields = [
            'last_name' => 'NewLastName',
            'first_name' => 'NewFirstName',
            'city' => 'NewCity',
        ];

        $service = new UserSocialiteService;

        $service->updateUserFieldsIfNotNull($user, $fields);

        $this->assertEquals('NewLastName', $user->last_name);
        $this->assertEquals('NewFirstName', $user->first_name);
        $this->assertEquals('NewCity', $user->city);
    }

    protected function getDatasetClass(): string
    {
        return EmptyDataSet::class;
    }
}
