<?php

namespace App\Models;

use App\Core\Model;

class Comment extends Model
{
    protected $table = 'comments';

    public function getForArticle($articleId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE article_id = :article_id ORDER BY created_at DESC");
        $stmt->execute(['article_id' => $articleId]);
        return $stmt->fetchAll();
    }
}
