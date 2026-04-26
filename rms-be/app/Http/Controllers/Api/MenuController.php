<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\TableUnit;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function currentMenu(): JsonResponse
    {
        $venue = Venue::query()->where('is_active', true)->firstOrFail();

        return $this->venueMenu($venue);
    }

    public function venueMenu(Venue $venue): JsonResponse
    {
        return response()->json([
            'venue' => $venue,
            'categories' => MenuCategory::query()
                ->where('venue_id', $venue->venue_id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'items' => MenuItem::query()
                ->with(['category', 'photos', 'tags', 'allergens'])
                ->where('venue_id', $venue->venue_id)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (MenuItem $item) => $this->toMenuDto($item)),
        ]);
    }

    public function tableMenu(TableUnit $table): JsonResponse
    {
        $table->load(['venue', 'qrCode']);

        return response()->json([
            'table' => $table,
            'menu' => $this->venueMenu($table->venue)->getData(true),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $item = MenuItem::create($data);
        $this->syncLabels($item, $request);
        $this->syncPrimaryPhoto($item, $request);

        return response()->json($this->toMenuDto($item->load(['category', 'photos', 'tags', 'allergens'])), 201);
    }

    public function update(Request $request, MenuItem $menuItem): JsonResponse
    {
        $data = $request->validate($this->rules(required: false));
        $menuItem->update($data);
        $this->syncLabels($menuItem, $request);
        $this->syncPrimaryPhoto($menuItem, $request);

        return response()->json($this->toMenuDto($menuItem->load(['category', 'photos', 'tags', 'allergens'])));
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $menuItem->delete();

        return response()->json(['deleted' => true]);
    }

    public function toggle(MenuItem $menuItem): JsonResponse
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return response()->json($this->toMenuDto($menuItem->load(['category', 'photos', 'tags', 'allergens'])));
    }

    private function rules(bool $required = true): array
    {
        $mode = $required ? 'required' : 'sometimes';

        return [
            'venue_id' => [$mode, 'uuid', 'exists:venues,venue_id'],
            'category_id' => [$mode, 'uuid', 'exists:menu_categories,category_id'],
            'name' => [$mode, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => [$mode, 'numeric', 'min:0'],
            'calories' => ['sometimes', 'integer', 'min:0'],
            'protein_g' => ['sometimes', 'numeric', 'min:0'],
            'carbs_g' => ['sometimes', 'numeric', 'min:0'],
            'fat_g' => ['sometimes', 'numeric', 'min:0'],
            'health_score' => ['sometimes', 'integer', 'between:0,100'],
            'is_available' => ['sometimes', 'boolean'],
            'admin_adjusted' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'image_url' => ['sometimes', 'url', 'max:2048'],
        ];
    }

    private function syncLabels(MenuItem $item, Request $request): void
    {
        if ($request->has('tag_ids')) {
            $item->tags()->sync($request->array('tag_ids'));
        }

        if ($request->has('allergen_ids')) {
            $item->allergens()->sync($request->array('allergen_ids'));
        }
    }

    private function syncPrimaryPhoto(MenuItem $item, Request $request): void
    {
        if (! $request->filled('image_url')) {
            return;
        }

        $photo = $item->photos()->orderBy('sort_order')->first();

        if ($photo) {
            $photo->update(['s3_url' => $request->string('image_url')->toString(), 'uploaded_at' => now()]);
            return;
        }

        $item->photos()->create([
            's3_url' => $request->string('image_url')->toString(),
            'sort_order' => 1,
            'uploaded_at' => now(),
        ]);
    }

    public function toMenuDto(MenuItem $item): array
    {
        $tagNames = $item->tags->pluck('name')->values();
        $photo = $item->photos->sortBy('sort_order')->first();

        return [
            'id' => $item->item_id,
            'name' => $item->name,
            'description' => $item->description ?? '',
            'price' => (float) $item->price,
            'category' => $item->category?->name,
            'image' => $photo?->s3_url,
            'imageUrl' => $photo?->s3_url ?? '',
            'tags' => $tagNames,
            'dietaryLabels' => $tagNames,
            'allergens' => $item->allergens->pluck('name')->values(),
            'nutritionCalories' => $item->calories,
            'nutritionProtein' => (float) $item->protein_g,
            'nutritionCarbs' => (float) $item->carbs_g,
            'spicy' => $tagNames->contains('Spicy'),
            'vegetarian' => $tagNames->contains('Vegetarian'),
            'vegan' => $tagNames->contains('Vegan'),
            'halal' => $tagNames->contains('Halal'),
            'glutenFree' => $tagNames->contains('Gluten-Free'),
            'available' => $item->is_available,
            'healthScore' => $item->health_score,
        ];
    }
}
