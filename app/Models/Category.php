<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'sno';

    protected $guarded = [];

    /**
     * Relationship with Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'sno');
    }

    /**
     * Parent category relationship
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'sno');
    }

    /**
     * Child categories relationship
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'sno');
    }

    /**
     * Get root-to-leaf lineage for a category (e.g. [Grandparent, Parent, Self])
     */
    public function getLineage()
    {
        $lineage = [];
        $curr = $this;
        $visited = [];

        while ($curr && !in_array($curr->sno, $visited, true)) {
            $visited[] = $curr->sno;
            array_unshift($lineage, $curr);
            $curr = $curr->parent;
        }

        return $lineage;
    }

    /**
     * Get flat, hierarchically ordered list of categories for a supplier or global.
     * Each item includes: level, parent_name, breadcrumb, display_label, and plain_label.
     */
    public static function getHierarchicalCategories($supplierId = null)
    {
        $query = self::with(['supplier', 'parent'])->orderBy('name', 'asc');
        if (!empty($supplierId)) {
            $query->where('supplier_id', $supplierId);
        }

        $all = $query->get();

        $byId = [];
        foreach ($all as $c) {
            $byId[$c->sno] = $c;
        }

        $childrenOf = [];
        $roots = [];
        foreach ($all as $c) {
            if (!empty($c->parent_id) && isset($byId[$c->parent_id])) {
                $childrenOf[$c->parent_id][] = $c;
            } else {
                $roots[] = $c;
            }
        }

        $result = collect();
        $traverse = function ($cat, $level = 0, $parentNames = []) use (&$traverse, &$result, &$childrenOf) {
            $cat->level = $level;
            $cat->parent_name = !empty($parentNames) ? end($parentNames) : null;
            $cat->breadcrumb = !empty($parentNames) ? implode(' › ', $parentNames) . ' › ' . $cat->name : $cat->name;
            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
            $prefix = $level > 0 ? '↳ ' : '';
            $suffix = $level > 0 && $cat->parent_name ? " (under {$cat->parent_name})" : '';
            $cat->display_label = $indent . $prefix . e($cat->name) . $suffix;
            $cat->plain_label = ($level > 0 ? str_repeat('   ', $level) . '↳ ' : '') . $cat->name . $suffix;

            $result->push($cat);

            if (isset($childrenOf[$cat->sno])) {
                $nextParents = array_merge($parentNames, [$cat->name]);
                foreach ($childrenOf[$cat->sno] as $child) {
                    $traverse($child, $level + 1, $nextParents);
                }
            }
        };

        foreach ($roots as $root) {
            $traverse($root, 0, []);
        }

        return $result;
    }
}
