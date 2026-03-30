<?php

class RateData {
    private PDO $conn;

    public function __construct(PDO $database_connection) {
        $this->conn = $database_connection;
    }

    public function getReviewTitle(): string {
        return "How was your shopping here?";
    }

    public function getPageTitle(): string {
        return "Aqua Mini Shop - Shopping Experience Review";
    }

    public function saveReview(string $user_name, int $rating, string $comment): bool {
        $stmt = $this->conn->prepare(
            "INSERT INTO reviews (product_name, user_name, rating, comment)
             VALUES (:product_name, :user_name, :rating, :comment)"
        );

        return $stmt->execute([
            'product_name' => $this->getReviewTitle(),
            'user_name'    => $user_name,
            'rating'       => $rating,
            'comment'      => $comment
        ]);
    }

    public function getAllReviews(int $limit = 20): array {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM reviews
             WHERE product_name = :product_name
             ORDER BY created_at DESC
             LIMIT :limit"
        );

        $stmt->bindValue(':product_name', $this->getReviewTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getReviewsForHomepage(int $limit = 3): array {
        return $this->getAllReviews($limit);
    }

    public function getReviewStats(): array {
        $stats = [
            'total_reviews' => 0,
            'avg_rating'    => '0.0'
        ];

        $stmtTotal = $this->conn->prepare(
            "SELECT COUNT(*) FROM reviews WHERE product_name = :product_name"
        );
        $stmtTotal->execute([
            'product_name' => $this->getReviewTitle()
        ]);
        $stats['total_reviews'] = (int)$stmtTotal->fetchColumn();

        $stmtAvg = $this->conn->prepare(
            "SELECT AVG(rating) FROM reviews WHERE product_name = :product_name"
        );
        $stmtAvg->execute([
            'product_name' => $this->getReviewTitle()
        ]);
        $avg = $stmtAvg->fetchColumn();
        $stats['avg_rating'] = number_format($avg ?? 0, 1);

        return $stats;
    }
}
