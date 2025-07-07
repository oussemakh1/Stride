<?php

namespace App\Controllers;

use Framework\Core\Controller;
use App\Models\{User};
use Framework\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $model = new User();
        $items = $model->all();
        $this->view('users.index', ['items' => $items]);
    }

    public function create()
    {
        $this->view('users.add');
    }

    public function store(Request $request)
    {
        $model = new User();
        $model->create($request->all());
        header('Location: /users');
    }

    public function show(int $id)
    {
        $model = new User();
        $item = $model->find($id);
        $this->view('users.show', ['item' => $item]);
    }

    public function edit(int $id)
    {
        $model = new User();
        $item = $model->find($id);
        $this->view('users.edit', ['item' => $item]);
    }

    public function update(int $id, Request $request)
    {
        $model = new User();
        $model->update($id, $request->all());
        header('Location: /users');
    }

    public function destroy(int $id)
    {
        $model = new User();
        $model->delete($id);
        header('Location: /users');
    }
}