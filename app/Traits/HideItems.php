<?php

namespace App\Traits;

use App\Domain\Models\Organization;
use App\Domain\Models\OrganizationBlacklist;

trait HideItems {

    public $blacklist;

    public function whitelistOrganizations(array $organization_ids) {
        $all_organization_ids = Organization::all()->pluck('id')->toArray();
        $blacklist = array_diff($all_organization_ids, $organization_ids);

        $this->addToBlacklist($blacklist);
    }

    public function addToBlacklist(array $organization_ids) {
        foreach($organization_ids as $organization_id) {
            $row = new OrganizationBlacklist();

            $row->organization_id = $organization_id;
            $row->hideable_type = $this->getTable();
            $row->hideable_id = $this->id;

            $row->save();
        }
    }

    public function removeBlacklist() {
        OrganizationBlacklist::where([
            'hideable_type' => $this->getTable(),
            'hideable_id' => $this->id,
        ])->delete();
    }
}
