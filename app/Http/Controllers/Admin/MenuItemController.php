<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        $items = MenuItem::all();

        $food = $items->where('category', 'FOOD')->values();
        $pizza = $items->where('category', 'PIZZA')->values();
        $drinks = $items->where('category', 'DRINKS')->values();

        return view('admin.menu.index', compact('items', 'food', 'pizza', 'drinks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:FOOD,PIZZA,DRINKS'],
            'price' => ['required', 'numeric', 'min:0'],
            'imageUrl' => ['nullable', 'url', 'max:500'],
            'description' => ['required', 'string', 'max:500'],
            'popular' => ['nullable', 'boolean'],
        ]);

        $validated['popular'] = $request->boolean('popular');

        MenuItem::create($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', "Item '{$validated['name']}' added to menu!");
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:FOOD,PIZZA,DRINKS'],
            'price' => ['required', 'numeric', 'min:0'],
            'imageUrl' => ['nullable', 'url', 'max:500'],
            'description' => ['required', 'string', 'max:500'],
            'popular' => ['nullable', 'boolean'],
        ]);

        $validated['popular'] = $request->boolean('popular');

        $menu->update($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', "Item '{$menu->name}' updated successfully!");
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $name = $menu->name;
        $menu->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', "Item '{$name}' deleted from menu.");
    }
}
