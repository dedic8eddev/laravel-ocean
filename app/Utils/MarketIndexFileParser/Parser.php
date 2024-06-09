<?php

namespace App\Utils\MarketIndexFileParser;

class Parser {
    private $config;

    private $context;

    public function __construct(array $config, $initContext) {
        $this->config = $config;
        $this->context = $initContext;
    }

    protected function getCurrentState() {
        try {
            return $this->context['state'];
        } catch (\Exception $e) {
            throw new \Exception("Context should contain a state key");
        }
    }

    public function process(array $record) {
        $currentState = $this->getCurrentState();

        $newContext = $this->config[$currentState]($this->context, $record);

        if (!$newContext) {
            throw new \Exception("State function should return the new context");
        }

        $this->context = $newContext;
    }

    public function getContext() {
        return $this->context;
    }
}