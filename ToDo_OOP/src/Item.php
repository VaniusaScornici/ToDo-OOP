<?php

namespace Iscornici\ToDoOop;

class Item
{
    public function __construct(
        protected string                $id,
        protected string                $content,
        protected string                $status,
        protected null|string|\DateTime $createdAt = null,
        protected ?\DateTime            $dueDate = null
    )
    {
        $this->createdAt = $this->createdAt ?? \DateTime::createFromFormat("d-m-Y H:i:s", date("d-m-Y H:i:s"));
    }

    public static function fromArray(array $itemArray): Item
    {
        return new Item(
            $itemArray['id'] ?? '<unknown>',
            $itemArray['content'] ?? '<unknown>',
            $itemArray['status'] ?? 'outstanding',
            isset($itemArray['due_date']) ? \DateTime::createFromFormat("d-m-Y H:i:s", $itemArray['due_date']) : null,
            isset($itemArray['created_at']) ? \DateTime::createFromFormat("d-m-Y H:i:s", $itemArray['created_at']) : '<unknown>'
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTime|string|null
    {
        return $this->createdAt;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setContent(string $content): Item
    {
        $this->content = $content;
        return $this;
    }

    public function setStatus(string $status): Item
    {
        $this->status = $status;
        return $this;
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'content' => $this->getContent(),
            'status' => $this->getStatus(),
            'created_at' => $this->getCreatedAt()?->format("d-m-Y H:i:s"),
            'due_date' => $this->getDueDate()?->format("d-m-Y H:i:s")
        ];
    }
}