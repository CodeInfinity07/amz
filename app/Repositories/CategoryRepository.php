<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getAllCategories(): Collection
    {
        return Category::with('childrenCategories')->get();
    }


    /**
     * @inheritdoc
     */
    public function findById(int $id): ?Category
    {
        $category = Category::with('childrenCategories')->findOrFail($id);

        return $category;
    }

    /**
     * @inheritdoc
     */
    public function findByName(string $name): ?Category
    {
        $category = Category::with('childrenCategories')->where('name', $name)->firstOrFail();

        return $category;
    }


    /**
     * @inheritdoc
     */
    public function findOrCreate(string $name, ?string $parentName = null): ?Category
    {
        $category = Category::where('name', $name)->first();

        if ($category) {
            return $category;
        }

        $parent_id = null;
        if ($parentName) {
            $parentCategory = Category::where('name', $parentName)->first();
            $parent_id = $parentCategory ? $parentCategory->id : null;
        }

        return Category::create([
            'name' => $name,
            'category_id' => $parent_id
        ]);
    }


    /**
     * @inheritdoc
     */
    public function generateCategories(array $categories): int
    {
        $parentName = null;
        $foundCategory = null;
        foreach ($categories as $categoryName) {
            $foundCategory = $this->findOrCreate($categoryName, $parentName);
            $parentName = $foundCategory->name;
        }
        return $foundCategory ?  $foundCategory->id : 0;
    }

    /**
     * @inheritdoc
     */
    public function getAllParent(Category $category): array
    {
        $i = 1;
        $categories[$i] =  $category->name;

        $parent_id = $category->category_id;
        while ($parent_id !== null) {

            $category = Category::find($parent_id);
            $parent_id = $category->category_id;

            $i++;
            $categories[$i] =  $category->name;
        }

        return $categories;
    }
}
