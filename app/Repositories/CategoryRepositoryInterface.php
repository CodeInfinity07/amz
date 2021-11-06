<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Category Repository
 */

interface CategoryRepositoryInterface
{

    /**
     * Fetch All Category
     * 
     * @return Collection
     */
    public function getAllCategories(): ?Collection;

    /**
     * Find category by Id
     */
    public function findById(int $id): ?Category;

    /**
     * Find category by Name
     * 
     * @return Category
     */
    public function findByName(string $name): ?Category;

    /**
     * Find category by Name and Parent Name or Create it if not exist
     * 
     * @return Category
     */
    public function findOrCreate(string $name, string $parentName = null): ?Category;

    /**
     * Generate Category hierarchy from array and return Id
     * 
     * @return int
     */
    public function generateCategories(array $categories): int;

    /**
     * Get all Parent Categories of single Category
     * 
     * @param Category
     * 
     * @return Array
     */
    public function getAllParent(Category $category): array;
}
