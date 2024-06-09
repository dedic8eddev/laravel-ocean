<?php

namespace App\Utils\MarketIndexFileParser\ClarksonsCSVParser;

use App\Utils\MarketIndexFileParser\Parser;

class CSVParser {
    const WAITING_FOR_NUMBER_TITLES = 'waiting_for_number_titles';
    const WAITING_FOR_NAMES         = 'waiting_for_names';
    const WAITING_FOR_ROW           = 'waiting_for_row';
    const WAITING_FOR_CURRENCIES    = "waiting_for_currencies";
    const EOF                       = 'end_of_file';
    const INITIALIZING_CHUNK        = 'initializing_chunk';

    private $records = null;

    public static function from($records) {
        return new CSVParser($records);

    }

    private function __construct($records) {
        $this->records = $records;
    }

    public function initParser($chunkSize) : Parser {
        return new Parser([
            // skip all rows before and number rows
            self::WAITING_FOR_NUMBER_TITLES => function($context, $record) {
                if ($record[0] === "" && $record[1] !== "") {
                    $context['state'] = self::WAITING_FOR_NAMES;
                }

                return $context;
            },
            // read names row
            self::WAITING_FOR_NAMES => function($context, $record) {
                if ($record[0] === "" && $record[1] !== "") {
                    $context['state'] = self::WAITING_FOR_CURRENCIES;
                    // we initialize the market index record here
                    $context['data'] = array_map(function($field) {
                        return [
                            "name" => $field,
                            "values" => [],
                        ];
                    }, array_slice($record,1 ));
                }

                return $context;
            },
            // read currencies row
            self::WAITING_FOR_CURRENCIES => function ($context, $record) {
                $data = $context['data'];

                return array_merge($context, [
                    "data" => $data,
                    "state" => self::INITIALIZING_CHUNK
                ]);
            },
            // initialize a chunk of rows
            self::INITIALIZING_CHUNK => function($context, $record) {
                // check if we've reached the end of input
                // this will be denoted by an empty record
                if ($record[0] === "" && $record[1] === "") {
                    return array_merge($context, [
                        "state" => self::EOF
                    ]);
                }

                $date = $record[0];
                $data = $context['data'];
                $data["rows"] = 1;

                foreach (array_slice($record,1) as $index=>$field) {
                    $data[$index]["values"] = [[
                        "value" => $field,
                        "value_date" => $date
                    ]];
                }

                return array_merge($context, [
                    "state" => self::WAITING_FOR_ROW,
                    "data" => $data
                ]);

            },
            // read a row
            self::WAITING_FOR_ROW => function($context, $record) use ($chunkSize) {
                // check if we've reached the end of input
                // this will be denoted by an empty record
                if ($record[0] === "" && $record[1] === "") {
                    return array_merge($context, [
                        "state" => self::EOF
                    ]);
                }

                // parse the record values
                // a value record is in the following format
                // DATE, index1-value, index2-value, ..., indexN-value
                $date = $record[0]; // get the date
                $data = $context['data'];
                $data["rows"]++;
                // add the value to the corresponding index.
                foreach (array_slice($record,1) as $index=>$field) {
                    $data[$index]["values"][] = [
                        "value" => $field,
                        "value_date" => $date
                    ];
                }

                return array_merge($context, [
                    // if we've reached the chunk size limit, we move to the chunk initialization state,
                    // else we keep reading rows, until we do so
                    "state" => $data['rows'] === $chunkSize? self::INITIALIZING_CHUNK: self::WAITING_FOR_ROW,
                    "data" => $data
                ]);
            },
            // the input has ended
            self::EOF => function ($context, $record) {
                // we've reached the end of file, and will skip all rows.
                return $context;
            }
        ], ["state" => self::WAITING_FOR_NUMBER_TITLES, "data" => []]);
    }

    public function getParserState(Parser $parser): string {
        return $parser->getContext()['state'];
    }

    public function getParserData(Parser $parser) {
        return $parser->getContext()['data'];
    }

    public function getDataChunked($chunkSize, $callback) {
        $parser = $this->initParser($chunkSize);

        foreach ($this->records as $record) {
            $prevState = $this->getParserState($parser);
            $parser->process($record);
            $nextState = $this->getParserState($parser);

            if (
                // either the chunk size length was reached or we have no more rows
                $prevState === self::WAITING_FOR_ROW && $prevState !== $nextState
            ) {
                $callback($this->getParserData($parser));
            }
        }
    }
}