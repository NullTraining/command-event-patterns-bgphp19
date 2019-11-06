<?php

declare(strict_types=1);

namespace DumbJson;

abstract class JsonRepository
{
    /** @var string|false */
    private $env;

    public function __construct()
    {
        $this->env = getenv('APP_ENV');
    }

    public function truncate(): void
    {
        $this->setTableContent([]);
    }

    abstract protected function serialize($entity): array;

    abstract protected static function deserialize(array $data);

    abstract protected function getEntityId($entity): string;

    abstract protected function getTableName(): string;

    protected function add($entity): void
    {
        $content = $this->getTableContent();

        $id           = $this->getEntityId($entity);
        $content[$id] = $this->serialize($entity);

        $this->setTableContent($content);
    }

    protected function find(string $id)
    {
        $content = $this->getTableContent();

        if (array_key_exists($id, $content)) {
            return $this->deserialize($content[$id]);
        }

        return null;
    }

    private function setTableContent(array $content): void
    {
        $filename = $this->getFileName();

        $json = json_encode($content, JSON_PRETTY_PRINT);

        file_put_contents($filename, $json);
    }

    private function getTableContent(): array
    {
        $filename = $this->getFileName();

        if (false === is_file($filename)) {
            $this->setTableContent([]);

            return [];
        }

        $json = file_get_contents($filename);

        return json_decode($json, true);
    }

    private function getFileName(): string
    {
        return __DIR__.'/../../data/'.$this->env.'/'.$this->getTableName().'.json';
    }

    protected function getResults(): array
    {
        $content = $this->getTableContent();

        $result = [];

        foreach ($content as $item) {
            $result[] = $this->deserialize($item);
        }

        return $result;
    }
}
