<?php
// Cause verisini API'ye geri döndürmek için kullanılan Response DTO'su

class CauseResponse
{
    public int $id;
    public int $problem_id;
    public ?int $parent_id;
    public string $title;
    public int $is_root_cause;
    public ?string $action_plan;
    public ?string $created_at;
    public ?string $updated_at;
    /** @var CauseResponse[] */
    public array $children = [];

    public function __construct(array $row)
    {
        $this->id = (int)$row['id'];
        $this->problem_id = (int)$row['problem_id'];
        $this->parent_id = isset($row['parent_id']) ? (int)$row['parent_id'] : null;
        $this->title = $row['title'];
        $this->is_root_cause = isset($row['is_root_cause']) ? (int)$row['is_root_cause'] : 0;
        $this->action_plan = $row['action_plan'] ?? null;
        $this->created_at = $row['created_at'] ?? null;
        $this->updated_at = $row['updated_at'] ?? null;
    }

    public function addChild(CauseResponse $child): void
    {
        $this->children[] = $child;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function toArray(): array
    {
        $out = [
            'id' => $this->id,
            'problem_id' => $this->problem_id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'is_root_cause' => $this->is_root_cause,
            'action_plan' => $this->action_plan,
        ];

        if (!empty($this->children)) {
            $out['children'] = array_map(fn($c) => $c->toArray(), $this->children);
        }

        if ($this->created_at !== null) $out['created_at'] = $this->created_at;
        if ($this->updated_at !== null) $out['updated_at'] = $this->updated_at;

        return $out;
    }
}
