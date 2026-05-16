<?php

namespace Backend;

class Token
{
    private $db;

    private const PLANS = [
        'starter' => ['amount' => 4000, 'tokens' => 500, 'label' => 'Scale Plan'],
        'pro' => ['amount' => 7000, 'tokens' => 1000, 'label' => 'Empire Plan'],
    ];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getPlans()
    {
        return self::PLANS;
    }

    public function purchase($storeId, $plan)
    {
        if (!isset(self::PLANS[$plan])) {
            return ['success' => false, 'error' => 'Invalid plan'];
        }

        $tokensToAdd = self::PLANS[$plan]['tokens'];

        $storeStmt = $this->db->prepare("SELECT token_balance FROM stores WHERE id = ?");
        $storeStmt->execute([$storeId]);
        $store = $storeStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$store) {
            return ['success' => false, 'error' => 'Store not found'];
        }

        $newBalance = ((int) $store['token_balance']) + $tokensToAdd;

        $this->db->beginTransaction();
        try {
            $update = $this->db->prepare("UPDATE stores SET token_balance = ?, plan = ?, updated_at = datetime('now') WHERE id = ?");
            $update->execute([$newBalance, $plan, $storeId]);

            $log = $this->db->prepare("INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, 'credit', ?, ?, datetime('now'))");
            $log->execute([
                bin2hex(random_bytes(12)),
                $storeId,
                $tokensToAdd,
                self::PLANS[$plan]['label'] . ' token purchase',
            ]);

            $this->db->commit();
            return [
                'success' => true,
                'token_balance' => $newBalance,
                'added' => $tokensToAdd,
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => 'Failed to complete token purchase'];
        }
    }

    public function deductForOrder($slug)
    {
        $storeStmt = $this->db->prepare("SELECT id, token_balance, contact_phone, name FROM stores WHERE slug = ? AND is_active = 1");
        $storeStmt->execute([$slug]);
        $store = $storeStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$store) {
            return ['success' => false, 'error' => 'Store not found'];
        }

        if ((int) $store['token_balance'] <= 0) {
            return ['success' => false, 'error' => 'Order limit reached', 'code' => 'NO_TOKENS'];
        }

        $newBalance = ((int) $store['token_balance']) - 1;
        $number = preg_replace('/\D+/', '', (string) ($store['contact_phone'] ?? ''));

        if ($number === '') {
            return ['success' => false, 'error' => 'Store has no WhatsApp number configured'];
        }

        $message = rawurlencode("Hi! I'm interested in placing an order from {$store['name']}.");
        $waUrl = "https://wa.me/{$number}?text={$message}";

        $this->db->beginTransaction();
        try {
            $update = $this->db->prepare("UPDATE stores SET token_balance = ?, updated_at = datetime('now') WHERE id = ? AND token_balance > 0");
            $update->execute([$newBalance, $store['id']]);

            if ($update->rowCount() === 0) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Order limit reached', 'code' => 'NO_TOKENS'];
            }

            $log = $this->db->prepare("INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, 'debit', 1, ?, datetime('now'))");
            $log->execute([
                bin2hex(random_bytes(12)),
                $store['id'],
                'WhatsApp order redirect',
            ]);

            $this->db->commit();
            return [
                'success' => true,
                'remainingTokens' => $newBalance,
                'whatsappUrl' => $waUrl,
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => 'Could not process order'];
        }
    }

    public function history($storeId, $limit = 50)
    {
        $stmt = $this->db->prepare("SELECT * FROM token_transactions WHERE store_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $storeId, \PDO::PARAM_STR);
        $stmt->bindValue(2, (int) $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
