<?php

namespace Framework\Core;

trait SoftDeletes
{
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected $withTrashed = false;
    protected $onlyTrashed = false;

    public function bootSoftDeletes(): void
    {
        // This method would typically register global scopes
        // For now, we'll rely on the where clauses in the query builder
    }

    public function delete(): bool
    {
        $this->fireEvent('deleting', $this);
        $result = $this->update($this->id, ['deleted_at' => date('Y-m-d H:i:s')]);
        if ($result) {
            $this->fireEvent('deleted', $this);
        }
        return $result;
    }

    public function restore(): bool
    {
        $this->fireEvent('restoring', $this);
        $result = $this->update($this->id, ['deleted_at' => null]);
        if ($result) {
            $this->fireEvent('restored', $this);
        }
        return $result;
    }

    public function withTrashed(): self
    {
        $this->withTrashed = true;
        $this->onlyTrashed = false;
        return $this;
    }

    public function onlyTrashed(): self
    {
        $this->onlyTrashed = true;
        $this->withTrashed = false;
        return $this;
    }
}
