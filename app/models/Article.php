<?php

namespace App\Models;

use App\Core\Model;

class Article extends Model
{
    protected $table = 'articles';

    public function getPublished($limit = 10)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'published' ORDER BY created_at DESC LIMIT :limit");
        // PDO needs integers for LIMIT, use bindValue
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }
}
