<?php

namespace App\Controllers;

use Framework\Core\ApiController;
use App\Models\{Post};
use Framework\Http\Request;

class PostController extends ApiController
{
    public function index()
    {
        $model = new Post();
        $items = $model->all();
        $this->json($items);
    }

    public function store(Request $request)
    {
        $model = new Post();
        $newItem = $model->create($request->all());
        $this->json($newItem, 201);
    }

    public function show(int $id)
    {
        $model = new Post();
        $item = $model->find($id);
        if (!$item) {
            $this->json(['message' => 'Not Found'], 404);
        }
        $this->json($item);
    }

    public function update(int $id, Request $request)
    {
        $model = new Post();
        $updated = $model->update($id, $request->all());
        if (!$updated) {
            $this->json(['message' => 'Not Found or No Changes'], 404);
        }
        $this->json(['message' => 'Updated successfully']);
    }

    public function destroy(int $id)
    {
        $model = new Post();
        $deleted = $model->delete($id);
        if (!$deleted) {
            $this->json(['message' => 'Not Found'], 404);
        }
        $this->json(['message' => 'Deleted successfully']);
    }
}