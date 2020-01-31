<?php
/**
 * Closure.php created by Patrik Václavek
 */

namespace App\Structures\ClosureTable;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Closure
 * @package App\Structures\ClosureTable
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class Closure extends Model
{
    /**
     * @var string ANCESTOR COLUMN NAME
     */
    const COLUMN_ANCESTOR = 'ancestor_id';

    /**
     * @var string DESCENDANT COLUMN NAME
     */
    const COLUMN_DESCENDANT = 'descendant_id';

    /**
     * @var string DEPTH COLUMN NAME
     */
    const COLUMN_DEPTH = 'depth';

    /**
     * Has ancestor?
     *
     * @return bool
     */
    public function hasAncestor(): bool
    {
        return boolval($this->attributes[self::COLUMN_ANCESTOR] ?? false);
    }


    /**
     * Set identifier of the ancestor.
     *
     * @param int $id
     * @return $this
     */
    public function setAncestor(int $id)
    {
        $this->setAttribute(self::COLUMN_ANCESTOR, $id);

        return $this;
    }


    /**
     * Set identifier of the descendant.
     *
     * @param int $id
     * @return $this
     */
    public function setDescendant(int $id)
    {
        $this->setAttribute(self::COLUMN_DESCENDANT, $id);

        return $this;
    }


    /**
     * Set the depth.
     *
     * @param int $depth
     * @return $this
     */
    public function setDepth(int $depth)
    {
        $this->setAttribute(self::COLUMN_DEPTH, $depth);

        return $this;
    }


    /**
     * Get ancestor.
     *
     * @return int
     */
    public function getAncestor(): int
    {
        return $this->getAttribute(self::COLUMN_ANCESTOR);
    }


    /**
     * Get descendant.
     *
     * @return int
     */
    public function getDescendant(): int
    {
        return $this->getAttribute(self::COLUMN_DESCENDANT);
    }


    /**
     * Return ancestor column name.
     *
     * @return string
     */
    public function getAncestorColumn(): string
    {
        return self::COLUMN_ANCESTOR;
    }


    /**
     * Return descendant column name.
     *
     * @return string
     */
    public function getDescendantColumn(): string
    {
        return self::COLUMN_DESCENDANT;
    }


    /**
     * Return depth column name.
     *
     * @return string
     */
    public function getDepthColumn(): string
    {
        return self::COLUMN_DEPTH;
    }


    /**
     * Return qualified ancestor column name.
     *
     * @return string
     */
    public function getQualifiedAncestorColumn(): string
    {
        return $this->getTable() . '.' . $this->getAncestorColumn();
    }


    /**
     * Return qualified descendant column name.
     *
     * @return string
     */
    public function getQualifiedDescendantColumn(): string
    {
        return $this->getTable() . '.' . $this->getDescendantColumn();
    }


    /**
     * Return qualified depth column name.
     *
     * @return string
     */
    public function getQualifiedDepthColumn(): string
    {
        return $this->getTable() . '.' . $this->getDepthColumn();
    }


    /**
     * Inserts new node into the closure table.
     *
     * @param int $ancestorId
     * @param int $descendantId
     */
    public function insertClosure(int $ancestorId, int $descendantId)
    {
        $query = "
            INSERT INTO {$this->getTable()} ({$this->getAncestorColumn()}, {$this->getDescendantColumn()}, {$this->getDepthColumn()})
            SELECT ct.{$this->getAncestorColumn()}, {$descendantId}, ct.{$this->getDepthColumn()}+1
            FROM {$this->getTable()} AS ct
            WHERE ct.{$this->getDescendantColumn()} = {$ancestorId}
            UNION ALL
            SELECT {$descendantId}, {$descendantId}, 0
        ";

        \DB::insert($query);
    }


    /**
     * Make a node a descendant of another ancestor or makes it a root node.
     *
     * @param int $id
     * @return void
     * @throws \InvalidArgumentException
     */
    public function changeAncestor(int $id = null)
    {
        // prevent change when ancestor is the same.
        if ($this->hasAncestor() && $this->getAncestor() === $id) {
            return;
        }

        $this->unbindRelationships();

        if (is_null($id)) {
            return;
        }

        $table = $this->getTable();
        $query = "
            INSERT INTO {$table} ({$this->getAncestorColumn()}, {$this->getDescendantColumn()}, {$this->getDepthColumn()})
            SELECT ct.{$this->getAncestorColumn()}, sct.{$this->getDescendantColumn()}, ct.{$this->getDepthColumn()}+sct.{$this->getDepthColumn()}+1
            FROM {$table} AS ct
            CROSS JOIN {$table} AS sct
            WHERE ct.{$this->getDescendantColumn()}=? AND sct.{$this->getAncestorColumn()}=?
        ";

        \DB::insert($query, [$id, $this->getDescendant()]);
    }


    /**
     * Unbind current relationships.
     *
     * @return void
     */
    protected function unbindRelationships()
    {
        $descendant = $this->getDescendant();

        $query = "
            DELETE FROM {$this->getTable()}
            WHERE {$this->getDescendantColumn()} IN (
                SELECT {$this->getDescendantColumn()} FROM {$this->getTable()}
                WHERE {$this->getAncestorColumn()} = ?
            )
            AND {$this->getAncestorColumn()} IN (
                SELECT {$this->getAncestorColumn()} FROM {$this->getTable()}
                WHERE {$this->getDescendantColumn()} = ?
                AND {$this->getAncestorColumn()} <> {$this->getDescendantColumn()}
            )
        ";

        \DB::delete($query, [$descendant, $descendant]);
    }
}
