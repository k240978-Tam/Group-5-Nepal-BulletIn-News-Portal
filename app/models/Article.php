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

    public function findByIdWithDetails(int $id)
    {
        $stmt = $this->db->prepare("SELECT a.*, u.name as author_name, c.name as category_name FROM {$this->table} a LEFT JOIN users u ON a.author_id = u.id LEFT JOIN categories c ON a.category_id = c.id WHERE a.id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getTags(int $articleId)
    {
        $stmt = $this->db->prepare("SELECT t.name FROM tags t JOIN article_tags at ON t.id = at.tag_id WHERE at.article_id = :article_id");
        $stmt->execute(['article_id' => $articleId]);
        return array_column($stmt->fetchAll(), 'name');
    }

    public function getComments(int $articleId)
    {
        $stmt = $this->db->prepare("SELECT c.*, u.name as user_name FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = :article_id AND c.status = 'approved' ORDER BY c.created_at DESC");
        $stmt->execute(['article_id' => $articleId]);
        return $stmt->fetchAll();
    }

    public function getRelatedArticles(int $categoryId, int $excludeId, int $limit = 3)
    {
        $stmt = $this->db->prepare("SELECT id, title, image_url, created_at FROM {$this->table} WHERE category_id = :category_id AND id != :exclude_id AND status = 'published' ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', $excludeId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getArticlesByCategory(int $categoryId, int $limit = 50)
    {
        $stmt = $this->db->prepare("SELECT a.id, a.title, a.summary, a.image_url, a.created_at, c.name as category_name FROM {$this->table} a LEFT JOIN categories c ON a.category_id = c.id WHERE a.category_id = :category_id AND a.status = 'published' ORDER BY a.created_at DESC LIMIT :limit");
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentNewsExcluding(int $limit = 5, int $excludeId = 0)
    {
        $stmt = $this->db->prepare("SELECT a.id, a.title, a.image_url, a.created_at, c.name as category_name FROM {$this->table} a LEFT JOIN categories c ON a.category_id = c.id WHERE a.id != :exclude_id AND a.status = 'published' ORDER BY a.created_at DESC LIMIT :limit");
        $stmt->bindValue(':exclude_id', $excludeId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function incrementViews(int $id)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET views = views + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function search(string $query)
    {
        $searchTerm = '%' . $query . '%';
        $stmt = $this->db->prepare("SELECT a.id, a.title, a.summary, a.image_url, a.created_at, c.name as category_name FROM {$this->table} a LEFT JOIN categories c ON a.category_id = c.id WHERE (a.title LIKE :query_title OR a.content LIKE :query_content) AND a.status = 'published' ORDER BY a.created_at DESC");
        $stmt->execute([
            'query_title' => $searchTerm,
            'query_content' => $searchTerm,
        ]);
        return $stmt->fetchAll();
    }
}
