<?php

namespace Iscornici\ToDoOop;

use LogicException;

class App
{
    private array $items = [];

    public function __construct(
        protected string $path,
        protected string $prefix
    ) {
        $this->items = $this->getItems();
    }
    public function run()
    {
        while ($cmd = readline("todo>")) {
            $this->updateItems();

            try {
                match ($cmd) {
                    "list" => $this->listItems(),
                    'help' => $this->help(),
                    "add" => $this->addItem(readline("new item> "), readline("due-date> ")),
                    "delete" => $this->deleteItem(readline("item to delete> ")),
                    "edit-item" => $this->editItem(readline("item ID> "), readline("item new content> ")),
                    "set-status" => $this->setStatus(readline("item ID> "), readline("item new status> ")),
                    "search-items" => $this->searchItems(readline("content to search> ")),
                    default => print "Command $cmd not supported" . PHP_EOL
                };
            } catch (\Throwable $e) {
                print PHP_EOL . "SAVE" . PHP_EOL;
                print $e->getMessage() . PHP_EOL . PHP_EOL;
                $this->saveItems();
            }
        }
        $this->saveItems();
    }

    public function help()
    {
        print "Available commands: list, add, delete, set-status, edit-item, search_items, help" . PHP_EOL;
    }

    public function deleteItem(string $idToDelete): void
    {
        if (empty($idToDelete)) {
            throw new LogicException("You didn't provide item ID to delete.");
        }

        $filteredItems = array_filter($this->items, fn (Item $item) => $item->getId() !== $idToDelete);

        if (count($this->items) > count($filteredItems)) {
            print "Item $idToDelete was deleted" . PHP_EOL . PHP_EOL;
        } else {
            print "Nothing to delete" . PHP_EOL . PHP_EOL;
        }

        $this->items = $filteredItems;
    }

    public function addItem(string $content, string $dueDate): Item
    {
        if (empty($content)) {
            throw new LogicException("You didn't provide item content.");
        }

        $lastId = 0;

        if (count($this->items) > 0) {
            $lastItems = $this->items[count($this->items) - 1];
            $lastId = (int)str_replace($this->prefix, "", $lastItems->getId());
        }

        $item = new Item(
            $this->prefix . ($lastId + 1),
            $content,
            'new',
            $dueDate === "" ? null : \DateTime::createFromFormat("d-m-Y H:i:s", $dueDate),
            null
        );

        $this->items[] = $item;

        print "Item {$item->getId()} was added." . PHP_EOL . PHP_EOL;
        return $item;
    }


    public function listItems(): void
    {
        print "## Todo items ##" . PHP_EOL;

        if (empty($this->items)) {
            print "Nothing here yet..." . PHP_EOL . PHP_EOL;
            return;
        }

        foreach ($this->items as $item) {
            $this->printItem($item);
        }
    }

    public function printItem(Item $item): void
    {
        $state = $item->isDone() ? 'X' : ' ';

        print " - [$state] {$item->getId()} from ";
        print $item->getCreatedAt()->format("d-M-Y H:i:s");
        print "\n";
        print "   Content  : {$item->getContent()}" . PHP_EOL;
        print "   Status   : {$item->getStatus()}" . PHP_EOL;
        if (!empty($item->getDueDate())) {
            print "Due Dste: {$item->getDueDate()->format("d-M-Y H:i:s")}" . PHP_EOL;
        } else {
            print "\n";
        }
    }

    public function getItems(): array
    {
        if (!file_exists($this->path)) {
            $this->saveItems();
        }

        $arrayOfItems = json_decode(file_get_contents($this->path), true);
        return array_map(fn ($item) => Item::fromArray($item), $arrayOfItems);
    }

    public function saveItems(): void
    {
        $itemsArray = array_map(fn (Item $item) => $item->toArray(), $this->items);
        file_put_contents($this->path, json_encode(array_values($itemsArray), JSON_PRETTY_PRINT));
    }

    public function editItem(string $idToEdit, string $newContent): void
    {

        if (!$newContent) {
            throw new LogicException("No content.");
        }
        $items = $this->getItems();
        if (empty($items)) {
            print "No items to update" . PHP_EOL . PHP_EOL;
            return;
        }
        foreach ($this->items as $item) {
            if ($item->getId() === $idToEdit) {
                $item->setContent($newContent);
                break;
            }
        }
            print "Item $idToEdit was edited" . PHP_EOL . PHP_EOL;
        }

    public function setStatus(string $idToSetStatus, string $newStatus): void
    {
        $items = $this->getItems();
        if (empty($items)) {
            print "No items to update" . PHP_EOL . PHP_EOL;
            return;
        }
        foreach ($this->items as $item) {
            if ($item->getId() === $idToSetStatus) {
                $item->setStatus($newStatus);
                break;
            }
        }
        print "$idToSetStatus's status updated" . PHP_EOL . PHP_EOL;
    }

    public function updateItems(): void
    {
        foreach ($this->items as $item) {
            if ($item->getStatus() !== 'done' && (!empty($item->getDueDate())) && $item->getDueDate() < date('d-M-Y H:i:s')) {
                $item->setStatus('outdated');
            }
        }
    }

    private function searchItems(string $content): void
    {
        $items = $this->getItems();
        if (empty($items)) {
            print "No items to update" . PHP_EOL . PHP_EOL;
            return;
        }
        $find = array_search($content, array_column($this->items, "content"));

        if (!$find) {
            throw new LogicException("This content doesn't exist.");
        }
        print_r($find);
    }
}