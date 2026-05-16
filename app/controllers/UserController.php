<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Comment;
use App\Core\Database;

class UserController extends Controller
{
    public function profile()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        $db = Database::getInstance()->getConnection();

        // Fetch user's comments with article info
        $stmt = $db->prepare("SELECT c.content, c.status, c.created_at, a.title, a.id as article_id 
                               FROM comments c
                               JOIN articles a ON c.article_id = a.id
                               WHERE c.user_id = ?
                               ORDER BY c.created_at DESC");
        $stmt->execute([$user['id']]);
        $comments = $stmt->fetchAll();

        // If journalist/editor/admin — fetch their article stats
        $article_stats = null;
        if (in_array($user['role'], ['admin', 'editor', 'journalist'])) {
            $s = $db->prepare("SELECT
                COUNT(*) as total,
                SUM(status='published') as published,
                SUM(status='draft') as drafts,
                SUM(status='pending') as pending,
                COALESCE(SUM(views),0) as total_views
                FROM articles WHERE author_id = ?");
            $s->execute([$user['id']]);
            $article_stats = $s->fetch();
        }

        // Avatar initials
        $initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($user['name'])))));
        $initials  = substr($initials, 0, 2);

        $role_colors = [
            'admin'      => ['bg' => '#fee2e2', 'color' => '#c0392b', 'label' => 'Administrator'],
            'editor'     => ['bg' => '#fef3c7', 'color' => '#92400e', 'label' => 'Editor'],
            'journalist' => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'label' => 'Journalist'],
            'user'       => ['bg' => '#f0fdf4', 'color' => '#166534', 'label' => 'Reader'],
        ];
        $rc = $role_colors[$user['role']] ?? $role_colors['user'];

        return $this->view('profile.index', [
            'user' => $user,
            'comments' => $comments,
            'article_stats' => $article_stats,
            'initials' => $initials,
            'rc' => $rc
        ]);
    }
}
