<?php

namespace App\Controllers;

use Framework\Core\ApiController;
use App\Models\{Product};
use Framework\Http\Request;

class ProductController extends ApiController
{
    public function index()
    {
        $items = Product::all();
        $this->json($items);
    }

    public function store(Request $request)
    {
        $newItem = Product::create($request->all());
        if ($newItem) {
            $this->json($newItem, 201);
        } else {
            $this->json(['message' => 'Failed to create item'], 500);
        }
    }

    public function show(int $id)
    {
        $item = Product::find($id);
        if (!$item) {
            $this->json(['message' => 'Not Found'], 404);
        }
        $this->json($item);
    }

    public function update(int $id, Request $request)
    {
        $updated = Product::update($id, $request->all());
        if (!$updated) {
            $this->json(['message' => 'Not Found or No Changes'], 404);
        }
        $this->json(['message' => 'Updated successfully']);
    }

    public function destroy(int $id)
    {
        $deleted = Product::delete($id);
        if (!$deleted) {
            $this->json(['message' => 'Not Found'], 404);
        }
        $this->json(['message' => 'Deleted successfully']);
    }
}