<?php

namespace bongrun\vk\core;

use SchemaStore;

class Generator
{
    const SCHEMA_BASE_SCHEMA = 'http://json-schema.org/draft-04/schema#';
    const SCHEMA_BASE_SCHEMA_OBJECTS = 'http://json-schema.org/draft-04/schemaobjects.json';
    const SCHEMA_OBJECTS_URL = 'https://raw.githubusercontent.com/VKCOM/vk-api-schema/master/objects.json';
    const SCHEMA_METHODS_URL = 'https://raw.githubusercontent.com/VKCOM/vk-api-schema/master/methods.json';
    const SCHEMA_RESPONSES_URL = 'https://raw.githubusercontent.com/VKCOM/vk-api-schema/master/responses.json';
    const SCHEMA_SCHEME_URL = 'https://raw.githubusercontent.com/VKCOM/vk-api-schema/master/schema.json';

    const PATH = __DIR__ . '/../../schema';

    protected function schemasUrs()
    {
        return [
            static::SCHEMA_BASE_SCHEMA,
            static::SCHEMA_BASE_SCHEMA_OBJECTS,
            static::SCHEMA_OBJECTS_URL,
            static::SCHEMA_METHODS_URL,
            static::SCHEMA_RESPONSES_URL,
            static::SCHEMA_SCHEME_URL,
        ];
    }

    private function getPath($url, $type = 'json')
    {
        return static::PATH . '/' . basename(trim($url, '#'), '.' . $type) . '-' . substr(md5($url . date('Y-m-d')), 0, 6) . '.' . $type;
    }

    protected function download()
    {
        foreach ($this->schemasUrs() as $url) {
            if (file_exists($this->getPath($url))) {
                continue;
            }
            file_put_contents($this->getPath($url), @file_get_contents($url));
        }
    }

    protected function initialSchema()
    {
        $store = new SchemaStore();
        foreach ($this->schemasUrs() as $url) {
            if (file_exists($this->getPath($url, 'sr'))) {
                continue;
            }
            $store->add($url, json_decode(file_get_contents($this->getPath($url))));
            $schema	= $store->get(static::SCHEMA_OBJECTS_URL);
            file_put_contents($this->getPath($url, 'sr'), serialize($schema));
        }
    }

    public function run()
    {
        $this->download();
        $this->initialSchema();
    }
}