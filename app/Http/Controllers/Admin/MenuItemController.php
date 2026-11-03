<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Setting;
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
        $menuBoardImage = Setting::get('menu_board_image', '/images/menu-card.jpg');

        return view('admin.menu.index', compact('items', 'food', 'pizza', 'drinks', 'menuBoardImage'));
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

    public function updateMenuBoard(Request $request): RedirectResponse
    {
        $request->validate([
            'menu_board_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:12288'],
            'menu_board_url' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('menu_board_file')) {
            $file = $request->file('menu_board_file');
            $filename = 'menu-board-' . time() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('images');
            if (!file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }
            $file->move($dest, $filename);
            $webPath = '/images/' . $filename;
            Setting::set('menu_board_image', $webPath);
            try {
                @copy(public_path('images/' . $filename), public_path('images/menu-card.jpg'));
            } catch (\Throwable $e) {}
        } elseif ($request->filled('menu_board_url')) {
            Setting::set('menu_board_image', trim($request->input('menu_board_url')));
        }

        return redirect()->route('admin.menu.index')
            ->with('success', 'Official Menu Board image updated successfully!');
    }
}
