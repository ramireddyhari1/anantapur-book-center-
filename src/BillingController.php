<?php

namespace App;

class BillingController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createInvoice($data) {
        try {
            $invoiceId = uniqid('inv_');
            $invoiceNo = 'ABC-' . date('Ymd') . '-' . rand(1000, 9999);
            $customerId = $data['customerId'] ?: null;
            $totalAmount = (float)$data['totalAmount'];
            $taxAmount = (float)$data['taxAmount'];
            $grandTotal = $totalAmount + $taxAmount;

            // Start Transaction
            $this->db->query("BEGIN");

            // Insert Invoice
            $this->db->query("INSERT INTO \"Invoice\" (id, \"invoiceNo\", \"customerId\", \"totalAmount\", \"taxAmount\", \"paymentStatus\") VALUES (?, ?, ?, ?, ?, ?)", [
                $invoiceId, $invoiceNo, $customerId, $grandTotal, $taxAmount, 'paid'
            ]);

            // Insert Items & Update Stock
            foreach ($data['items'] as $item) {
                $itemId = uniqid('invit_');
                $qty = (float)$item['qty'];
                $rate = (float)$item['price'];
                $taxRate = (float)$item['gstRate'];
                $itemTax = ($qty * $rate) * ($taxRate / 100);
                $total = ($qty * $rate) + $itemTax;

                $this->db->query("INSERT INTO \"InvoiceItem\" (id, \"invoiceId\", \"productId\", qty, rate, \"taxRate\", \"taxAmount\", total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
                    $itemId, $invoiceId, $item['id'], $qty, $rate, $taxRate, $itemTax, $total
                ]);

                // Deduct Stock
                $this->db->query("UPDATE \"Product\" SET \"stockQty\" = \"stockQty\" - ? WHERE id = ?", [$qty, $item['id']]);
            }

            // Create Ledger Entry if customer is not anonymous
            if ($customerId) {
                $ledgerId = uniqid('led_');
                $this->db->query("INSERT INTO \"AccountLedger\" (id, \"entityId\", \"entityType\", description, credit, referenceId) VALUES (?, ?, ?, ?, ?, ?)", [
                    $ledgerId, $customerId, 'customer', "Invoice " . $invoiceNo, $grandTotal, $invoiceId
                ]);
                
                // Update Customer Balance (assuming credit means they paid, but usually in ERP it depends on context)
                // For a cash sale, we record the sale.
            }

            $this->db->query("COMMIT");

            return ['success' => true, 'id' => $invoiceId];
        } catch (\Exception $e) {
            $this->db->query("ROLLBACK");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function saveCustomer($name, $phone, $email = null, $gstin = null, $address = null) {
        $id = uniqid('cust_');
        $this->db->query("INSERT INTO \"Customer\" (id, name, phone, email, gstin, address) VALUES (?, ?, ?, ?, ?, ?)", [$id, $name, $phone, $email, $gstin, $address]);
        return ['success' => true, 'id' => $id];
    }

    public function saveSupplier($name, $phone, $gstin = null, $address = null) {
        $id = uniqid('supp_');
        $this->db->query("INSERT INTO \"Supplier\" (id, name, phone, gstin, address) VALUES (?, ?, ?, ?, ?)", [$id, $name, $phone, $gstin, $address]);
        return ['success' => true, 'id' => $id];
    }

    public function recordPurchase($data) {
        try {
            $this->db->query("BEGIN");
            $supplierId = $data['supplierId'];
            $totalAmount = 0;

            foreach ($data['items'] as $item) {
                $qty = (float)$item['qty'];
                $rate = (float)$item['rate'];
                $lineTotal = $qty * $rate;
                $totalAmount += $lineTotal;

                // Update Stock and Cost Price
                $this->db->query("UPDATE \"Product\" SET \"stockQty\" = \"stockQty\" + ?, \"costPrice\" = ? WHERE id = ?", [$qty, $rate, $item['productId']]);
            }

            // Create Ledger Entry for Supplier
            $ledgerId = uniqid('led_');
            $this->db->query("INSERT INTO \"AccountLedger\" (id, \"entityId\", \"entityType\", description, debit, referenceId) VALUES (?, ?, ?, ?, ?, ?)", [
                $ledgerId, $supplierId, 'supplier', "Purchase Bill " . ($data['billNo'] ?? 'N/A'), $totalAmount, $data['billNo'] ?? null
            ]);

            // Update Supplier Balance
            $this->db->query("UPDATE \"Supplier\" SET balance = balance + ? WHERE id = ?", [$totalAmount, $supplierId]);

            $this->db->query("COMMIT");
            return ['success' => true];
        } catch (\Exception $e) {
            $this->db->query("ROLLBACK");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
