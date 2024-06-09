<?php

use App\Domain\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class UserSeeder extends Seeder
{

    private $update = false;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            ['Dimitris', 'dimitris@monospacelabs.com', 'eba9eedb-9971-464a-a994-aad300bf6cc1', true],
            ['Duke', 'duke@monospacelabs.com', '8f33e925-f438-483f-bef0-5b1695676b2b', true],
            ['Gen', 'genovefa@monospacelabs.com', 'c1f2a872-fa28-4290-8ace-5f5cbeb5e5bd', true],
            ['George', 'george@monospacelabs.com', '53679e68-062f-4b23-869d-80ff9387882d', true],
            ['KO', 'ko@monospacelabs.com', '99b2d108-fbaa-4188-a5f0-aad101573f5d', true],
            ['Maria', 'maria@monospacelabs.com', '5cd086b7-f6b9-4b40-a24e-892a5843e9af', true],
            ['Pap', 'pap@monospacelabs.com', '4b0863e4-dc5c-4986-9b45-5686c1a2823b', true],
            ['Pi', 'pi@monospacelabs.com', '15278d68-84fb-4310-9fac-0de3d7e71f6e', true],
            ['Sofia', 'sofia@monospacelabs.com', 'a5715e4e-e375-442a-9f68-aadf00b59b0f', true],
        ];

        $values = array_map(function ($value) {
            return array_combine(['name', 'email', 'sid', 'active'], $value);
        }, $values);

        foreach ($values as $value) {
            $model = User::query()->firstOrNew(Arr::only($value, 'sid'), $value);
            if (!$model->exists || $this->update) {
                $model->save();
            }
        }
    }
}
