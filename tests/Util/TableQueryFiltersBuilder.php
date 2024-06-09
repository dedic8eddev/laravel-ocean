<?php

namespace Tests\Util;

class TableQueryFiltersBuilder {
    protected $filters = [];

    protected $sorting = [];

    protected $paging = [];

    public function eq($name, $value) {
        return $this->filter($name, "=", $value);
    }

    public function oneOf($name, $value) {
        return $this->filter($name, "oneOf", $value);
    }

    public function filter($name, $operation, $value) {
        $this->filters[] = compact('name', 'operation', 'value');
        return $this;
    }

    public function sorting($name, $order) {
        $this->sorting[$name] = $order;
        return $this;
    }

    public function paging($per_page, $current_page) {
        $this->paging['per_page'] = $per_page;
        $this->paging['current_page'] = $current_page;
        return $this;
    }

    public function build() {
        return [
            "filters" => $this->filters,
            "sorting" => $this->sorting,
            "paging" => $this->paging
        ];
    }

    public static function instance() {
        return new TableQueryFiltersBuilder();
    }
}
