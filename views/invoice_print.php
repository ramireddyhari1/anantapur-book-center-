<?php
$id = $_GET['id'] ?? null;
if (!$id) exit('No invoice ID provided');

$invoice = $db->fetch("SELECT i.*, c.name as customer_name, c.phone as customer_phone, c.address as customer_address, c.gstin as customer_gstin 
                       FROM \"Invoice\" i 
                       LEFT JOIN \"Customer\" c ON i.\"customerId\" = c.id 
                       WHERE i.id = ?", [$id]);

if (!$invoice) exit('Invoice not found');

$items = $db->fetchAll("SELECT ii.*, p.name as product_name, p.hsn 
                        FROM \"InvoiceItem\" ii 
                        JOIN \"Product\" p ON ii.\"productId\" = p.id 
                        WHERE ii.\"invoiceId\" = ?", [$id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= $invoice['invoiceNo'] ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; margin: 0; padding: 40px; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #800000; pb-20; margin-bottom: 30px; padding-bottom: 10px; }
        .logo-area h1 { color: #800000; margin: 0; font-size: 28px; font-style: italic; font-weight: 800; }
        .invoice-info { text-align: right; }
        .invoice-info h2 { margin: 0; color: #666; font-size: 18px; }
        .address-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .address-box h3 { border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; font-size: 10px; text-transform: uppercase; color: #999; }
        table { w-full; border-collapse: collapse; margin-bottom: 30px; width: 100%; }
        th { background: #f9f9f9; text-align: left; padding: 10px; border-bottom: 1px solid #ddd; font-size: 10px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-area { display: flex; justify-content: flex-end; }
        .totals-table { width: 250px; }
        .totals-table tr td { padding: 5px 0; border: none; }
        .grand-total { font-size: 18px; font-weight: bold; color: #800000; border-top: 2px solid #800000 !important; pt-10; margin-top: 10px; }
        .footer { margin-top: 60px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #eee; padding-top: 20px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="background: #fff; padding: 10px; border-bottom: 1px solid #ddd; margin-bottom: 20px; text-align: center; display: flex; justify-content: center; gap: 10px;">
        <button onclick="window.print()" style="background: #800000; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">PRINT INVOICE</button>
        <?php 
        $msg = "Hello " . ($invoice['customer_name'] ?: 'Customer') . ", your invoice " . $invoice['invoiceNo'] . " for ₹" . number_format($invoice['totalAmount'], 2) . " from Anantapur Book Centre is ready.";
        $waLink = "https://wa.me/" . (preg_replace('/[^0-9]/', '', $invoice['customer_phone'] ?? '')) . "?text=" . urlencode($msg);
        ?>
        <a href="<?= $waLink ?>" target="_blank" style="background: #25D366; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none;">SHARE ON WHATSAPP</a>
    </div>

    <div class="header">
        <div class="logo-area">
            <h1>ANANTAPUR BOOK CENTRE</h1>
            <p>Main Road, Anantapur, Andhra Pradesh - 515001<br>GSTIN: 37AAAAA0000A1Z5 | Phone: +91 98765 43210</p>
        </div>
        <div class="invoice-info">
            <h2>TAX INVOICE</h2>
            <p><strong>No:</strong> <?= $invoice['invoiceNo'] ?><br><strong>Date:</strong> <?= date('d-M-Y', strtotime($invoice['date'])) ?></p>
        </div>
    </div>

    <div class="address-grid">
        <div class="address-box">
            <h3>Billed To</h3>
            <p><strong><?= htmlspecialchars($invoice['customer_name'] ?: 'Cash Customer') ?></strong></p>
            <?php if($invoice['customer_address']): ?><p><?= nl2br(htmlspecialchars($invoice['customer_address'])) ?></p><?php endif; ?>
            <?php if($invoice['customer_phone']): ?><p>Phone: <?= htmlspecialchars($invoice['customer_phone']) ?></p><?php endif; ?>
            <?php if($invoice['customer_gstin']): ?><p>GSTIN: <?= htmlspecialchars($invoice['customer_gstin']) ?></p><?php endif; ?>
        </div>
        <div class="address-box">
            <h3>Transport Details</h3>
            <p>Dispatch via: Local Delivery<br>E-Way Bill: Not Required</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="50">#</th>
                <th>Product Description</th>
                <th width="80">HSN</th>
                <th width="60" class="text-center">Qty</th>
                <th width="80" class="text-right">Rate</th>
                <th width="60" class="text-center">GST %</th>
                <th width="100" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $index => $item): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><strong><?= htmlspecialchars($item['product_name']) ?></strong></td>
                <td class="text-center"><?= htmlspecialchars($item['hsn'] ?: '-') ?></td>
                <td class="text-center"><?= $item['qty'] ?></td>
                <td class="text-right"><?= number_format($item['rate'], 2) ?></td>
                <td class="text-center"><?= $item['taxRate'] ?>%</td>
                <td class="text-right"><?= number_format($item['total'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals-area">
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">₹<?= number_format($invoice['totalAmount'] - $invoice['taxAmount'], 2) ?></td>
            </tr>
            <tr>
                <td>Tax (GST)</td>
                <td class="text-right">₹<?= number_format($invoice['taxAmount'], 2) ?></td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total</td>
                <td class="text-right">₹<?= number_format($invoice['totalAmount'], 2) ?></td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px;">
        <p><strong>Amount in words:</strong> Indian Rupees Only</p>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature required.<br>Thank you for shopping with Anantapur Book Centre!</p>
    </div>
</body>
</html>
