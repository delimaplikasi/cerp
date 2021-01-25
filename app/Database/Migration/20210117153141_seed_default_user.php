<?php
declare(strict_types=1);

use App\Config\Site;
use App\Model\User;
use App\Singleton\Database;
use App\Singleton\Password;
use Phinx\Migration\AbstractMigration;

final class SeedDefaultUser extends AbstractMigration
{
    public function up()
    {
        $user = new User(Database::connect());

        $user->insert([
            'code' => 'system',
            'name' => 'System',
            'email' => 'system@' . Site::$domain,
            'password' => Password::hash('1234567890'),
            'roles_to_add' => [
                'system'
            ],
        ]);

        $user->insert([
            'code' => 'developer',
            'name' => 'Developer',
            'email' => 'developer@' . Site::$domain,
            'password' => Password::hash('1234567890'),
            'roles_to_add' => [
                'user', 'developer'
            ]
        ]);

        $user->insert([
            'code' => 'administrator',
            'name' => 'Administrator',
            'email' => 'administrator@' . Site::$domain,
            'password' => Password::hash('1234567890'),
            'roles_to_add' => [
                'user', 'administrator'
            ]
        ]);

        $user->insert([
            'code' => 'user',
            'name' => 'User',
            'email' => 'user@' . Site::$domain,
            'password' => Password::hash('1234567890'),
            'roles_to_add' => [
                'user',
            ]
        ]);
    }

    public function down()
    {
        $user = new User(Database::connect());

        foreach ($user->addCondition('code', 'in', [
            'system', 'developer', 'administrator', 'user'
        ])->action('select') as $item) {
            $user->delete($item['id']);
        }
    }
}
