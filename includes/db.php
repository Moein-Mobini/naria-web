<?php
require_once __DIR__ . '/../config.php';

/**
 * تشخیص درایور: اگر pdo_sqlite موجود باشد از SQLite استفاده می‌کند،
 * در غیر این صورت از فایل JSON (بدون نیاز به هیچ اکستنشنی).
 */
function useSqlite() {
    static $ok = null;
    if ($ok === null) {
        $ok = extension_loaded('pdo_sqlite');
    }
    return $ok;
}

/* ============================================================
   لایه JSON (fallback برای ویندوز بدون SQLite)
   ============================================================ */
function jsonLoad() {
    if (!file_exists(JSON_DB)) {
        $seed = [
            'products' => [
                ['id'=>1,'name'=>'تنباکو ناریـا کلاسیک','description'=>'ترکیب اصیل و لوکس تنباکو ناریـا با عطر ملایم','price'=>450000,'category'=>'تنباکو','image'=>'','stock'=>50,'featured'=>1,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
                ['id'=>2,'name'=>'تنباکو ناریـا اسپشیال','description'=>'طعم خاص و ماندگار برای هوکا','price'=>520000,'category'=>'تنباکو','image'=>'','stock'=>40,'featured'=>1,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
                ['id'=>3,'name'=>'سیگار ناریـا پریموم','description'=>'بسته‌بندی لوکس و کیفیت بالا','price'=>380000,'category'=>'سیگار','image'=>'','stock'=>60,'featured'=>0,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
                ['id'=>4,'name'=>'اکسسوری هوکا طلایی','description'=>'ست کامل اکسسوری هوکا با طراحی ناریـا','price'=>890000,'category'=>'هوکا','image'=>'','stock'=>25,'featured'=>1,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
                ['id'=>5,'name'=>'شیشه هوکا ناریـا','description'=>'شیشه دست‌ساز با کیفیت پریمیوم','price'=>650000,'category'=>'هوکا','image'=>'','stock'=>30,'featured'=>0,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
                ['id'=>6,'name'=>'ویپ پاد ناریـا','description'=>'پاد و لوازم جانبی ویپ','price'=>720000,'category'=>'ویپ و پاد','image'=>'','stock'=>35,'featured'=>0,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
                ['id'=>7,'name'=>'زغال مخصوص هوکا','description'=>'زغال مرغوب و کم دود','price'=>180000,'category'=>'هوکا','image'=>'','stock'=>100,'featured'=>0,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
                ['id'=>8,'name'=>'کالکشن ناریـا ویژه','description'=>'پکیج کامل محصولات منتخب','price'=>1250000,'category'=>'تنباکو','image'=>'','stock'=>15,'featured'=>1,'active'=>1,'created_at'=>date('Y-m-d H:i:s')],
            ],
            'orders' => [],
            'order_items' => [],
            'next_product_id' => 9,
            'next_order_id' => 1,
            'next_item_id' => 1,
            'support_tickets' => [],
            'next_support_id' => 1,
        ];
        jsonSave($seed);
        return $seed;
    }
    $raw = file_get_contents(JSON_DB);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return jsonLoad(); // re-seed if corrupt
    }
    return $data;
}

function jsonSave($data) {
    file_put_contents(JSON_DB, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

/* ============================================================
   SQLite
   ============================================================ */
function getDB() {
    if (!useSqlite()) {
        return null; // callers must use helper functions below
    }
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        initSqlite($pdo);
    }
    return $pdo;
}

function initSqlite($pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL DEFAULT 0,
            category TEXT NOT NULL DEFAULT 'تنباکو',
            image TEXT DEFAULT '',
            stock INTEGER DEFAULT 100,
            featured INTEGER DEFAULT 0,
            active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_name TEXT NOT NULL,
            phone TEXT NOT NULL,
            address TEXT,
            notes TEXT,
            total REAL NOT NULL DEFAULT 0,
            status TEXT DEFAULT 'pending',
            payment_method TEXT DEFAULT '',
            payment_reference TEXT DEFAULT '',
            receipt_file TEXT DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            product_name TEXT,
            quantity INTEGER NOT NULL DEFAULT 1,
            price REAL NOT NULL
        );
    ");
    // Backward-compatible columns for an existing SQLite database.
    $columns = $pdo->query("PRAGMA table_info(orders)")->fetchAll();
    $columnNames = array_map(fn($c) => $c['name'], $columns);
    foreach ([['payment_method',"TEXT DEFAULT ''"],['payment_reference',"TEXT DEFAULT ''"],['receipt_file',"TEXT DEFAULT ''"]] as $col) {
        if (!in_array($col[0], $columnNames, true)) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN {$col[0]} {$col[1]}");
        }
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS support_tickets (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, phone TEXT NOT NULL, subject TEXT NOT NULL, message TEXT NOT NULL, status TEXT DEFAULT 'open', created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
    $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        $samples = [
            ['تنباکو ناریـا کلاسیک', 'ترکیب اصیل و لوکس تنباکو ناریـا با عطر ملایم', 450000, 'تنباکو', '', 50, 1],
            ['تنباکو ناریـا اسپشیال', 'طعم خاص و ماندگار برای هوکا', 520000, 'تنباکو', '', 40, 1],
            ['سیگار ناریـا پریموم', 'بسته‌بندی لوکس و کیفیت بالا', 380000, 'سیگار', '', 60, 0],
            ['اکسسوری هوکا طلایی', 'ست کامل اکسسوری هوکا با طراحی ناریـا', 890000, 'هوکا', '', 25, 1],
            ['شیشه هوکا ناریـا', 'شیشه دست‌ساز با کیفیت پریمیوم', 650000, 'هوکا', '', 30, 0],
            ['ویپ پاد ناریـا', 'پاد و لوازم جانبی ویپ', 720000, 'ویپ و پاد', '', 35, 0],
            ['زغال مخصوص هوکا', 'زغال مرغوب و کم دود', 180000, 'هوکا', '', 100, 0],
            ['کالکشن ناریـا ویژه', 'پکیج کامل محصولات منتخب', 1250000, 'تنباکو', '', 15, 1],
        ];
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image, stock, featured) VALUES (?,?,?,?,?,?,?)");
        foreach ($samples as $s) $stmt->execute($s);
    }
}

function formatPrice($price) {
    return number_format((float)$price) . ' ' . CURRENCY;
}

/* ============================================================
   API یکپارچه محصولات / سفارش‌ها (هم SQLite هم JSON)
   ============================================================ */

function db_getProducts($onlyActive = true, $featuredOnly = false, $category = '', $search = '') {
    if (useSqlite()) {
        $pdo = getDB();
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        if ($onlyActive) { $sql .= " AND active=1"; }
        if ($featuredOnly) { $sql .= " AND featured=1"; }
        if ($category !== '') { $sql .= " AND category=?"; $params[] = $category; }
        if ($search !== '') {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%";
        }
        $sql .= " ORDER BY featured DESC, id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    // JSON
    $data = jsonLoad();
    $list = $data['products'];
    if ($onlyActive) $list = array_filter($list, fn($p) => !empty($p['active']));
    if ($featuredOnly) $list = array_filter($list, fn($p) => !empty($p['featured']));
    if ($category !== '') $list = array_filter($list, fn($p) => ($p['category'] ?? '') === $category);
    if ($search !== '') {
        $list = array_filter($list, function($p) use ($search) {
            return stripos($p['name'] ?? '', $search) !== false
                || stripos($p['description'] ?? '', $search) !== false;
        });
    }
    $list = array_values($list);
    usort($list, function($a, $b) {
        if (($b['featured'] ?? 0) != ($a['featured'] ?? 0)) return ($b['featured'] ?? 0) - ($a['featured'] ?? 0);
        return ($b['id'] ?? 0) - ($a['id'] ?? 0);
    });
    return $list;
}

function db_getProduct($id) {
    $id = (int)$id;
    if (useSqlite()) {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    $data = jsonLoad();
    foreach ($data['products'] as $p) {
        if ((int)$p['id'] === $id) return $p;
    }
    return null;
}

function db_getCategories() {
    if (useSqlite()) {
        $pdo = getDB();
        return $pdo->query("SELECT DISTINCT category FROM products WHERE active=1 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    }
    $data = jsonLoad();
    $cats = [];
    foreach ($data['products'] as $p) {
        if (!empty($p['active']) && !empty($p['category'])) $cats[$p['category']] = true;
    }
    $cats = array_keys($cats);
    sort($cats);
    return $cats;
}

function db_saveProduct($fields, $id = 0) {
    if (useSqlite()) {
        $pdo = getDB();
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category=?, image=?, stock=?, featured=?, active=? WHERE id=?");
            $stmt->execute([$fields['name'], $fields['description'], $fields['price'], $fields['category'], $fields['image'], $fields['stock'], $fields['featured'], $fields['active'], $id]);
            return $id;
        }
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image, stock, featured, active) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$fields['name'], $fields['description'], $fields['price'], $fields['category'], $fields['image'], $fields['stock'], $fields['featured'], $fields['active']]);
        return (int)$pdo->lastInsertId();
    }
    $data = jsonLoad();
    if ($id > 0) {
        foreach ($data['products'] as &$p) {
            if ((int)$p['id'] === $id) {
                $p = array_merge($p, $fields);
                $p['id'] = $id;
                break;
            }
        }
        unset($p);
        jsonSave($data);
        return $id;
    }
    $newId = $data['next_product_id']++;
    $fields['id'] = $newId;
    $fields['created_at'] = date('Y-m-d H:i:s');
    $data['products'][] = $fields;
    jsonSave($data);
    return $newId;
}

function db_deleteProduct($id) {
    $id = (int)$id;
    $row = db_getProduct($id);
    if ($row && !empty($row['image']) && file_exists(UPLOAD_DIR . $row['image'])) {
        @unlink(UPLOAD_DIR . $row['image']);
    }
    if (useSqlite()) {
        getDB()->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
        return;
    }
    $data = jsonLoad();
    $data['products'] = array_values(array_filter($data['products'], fn($p) => (int)$p['id'] !== $id));
    jsonSave($data);
}

function db_toggleProduct($id, $field) {
    $id = (int)$id;
    if (!in_array($field, ['featured', 'active'])) return;
    if (useSqlite()) {
        getDB()->exec("UPDATE products SET $field = 1 - $field WHERE id=$id");
        return;
    }
    $data = jsonLoad();
    foreach ($data['products'] as &$p) {
        if ((int)$p['id'] === $id) {
            $p[$field] = empty($p[$field]) ? 1 : 0;
            break;
        }
    }
    unset($p);
    jsonSave($data);
}

function db_createOrder($customer, $items, $total) {
    if (useSqlite()) {
        $pdo = getDB();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, address, notes, total, status) VALUES (?,?,?,?,?,'pending')");
            $stmt->execute([$customer['name'], $customer['phone'], $customer['address'], $customer['notes'], $total]);
            $orderId = (int)$pdo->lastInsertId();
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?,?,?,?,?)");
            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['qty'], $item['price']]);
                $pdo->prepare("UPDATE products SET stock = CASE WHEN stock >= ? THEN stock - ? ELSE 0 END WHERE id = ?")
                    ->execute([$item['qty'], $item['qty'], $item['id']]);
            }
            $pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    // JSON
    $data = jsonLoad();
    $orderId = $data['next_order_id']++;
    $data['orders'][] = [
        'id' => $orderId,
        'customer_name' => $customer['name'],
        'phone' => $customer['phone'],
        'address' => $customer['address'],
        'notes' => $customer['notes'],
        'total' => $total,
        'status' => 'pending',
        'payment_method' => '',
        'payment_reference' => '',
        'receipt_file' => '',
        'created_at' => date('Y-m-d H:i:s'),
    ];
    foreach ($items as $item) {
        $data['order_items'][] = [
            'id' => $data['next_item_id']++,
            'order_id' => $orderId,
            'product_id' => $item['id'],
            'product_name' => $item['name'],
            'quantity' => $item['qty'],
            'price' => $item['price'],
        ];
        foreach ($data['products'] as &$p) {
            if ((int)$p['id'] === (int)$item['id']) {
                $p['stock'] = max(0, (int)$p['stock'] - (int)$item['qty']);
                break;
            }
        }
        unset($p);
    }
    jsonSave($data);
    return $orderId;
}

function db_getOrders($statusFilter = '') {
    if (useSqlite()) {
        $pdo = getDB();
        if ($statusFilter !== '') {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE status=? ORDER BY created_at DESC");
            $stmt->execute([$statusFilter]);
            return $stmt->fetchAll();
        }
        return $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    }
    $data = jsonLoad();
    $list = $data['orders'];
    if ($statusFilter !== '') {
        $list = array_filter($list, fn($o) => ($o['status'] ?? '') === $statusFilter);
    }
    $list = array_values($list);
    usort($list, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
    return $list;
}

function db_getOrder($id) {
    $id = (int)$id;
    if (useSqlite()) {
        $stmt = getDB()->prepare("SELECT * FROM orders WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    $data = jsonLoad();
    foreach ($data['orders'] as $o) {
        if ((int)$o['id'] === $id) return $o;
    }
    return null;
}

function db_getOrderItems($orderId) {
    $orderId = (int)$orderId;
    if (useSqlite()) {
        $stmt = getDB()->prepare("SELECT * FROM order_items WHERE order_id=?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    $data = jsonLoad();
    return array_values(array_filter($data['order_items'], fn($i) => (int)$i['order_id'] === $orderId));
}


function db_updateOrderPayment($id, $method, $reference = '', $receiptFile = '') {
    $id = (int)$id;
    if (useSqlite()) {
        getDB()->prepare("UPDATE orders SET payment_method=?, payment_reference=?, receipt_file=? WHERE id=?")
            ->execute([$method, $reference, $receiptFile, $id]);
        return;
    }
    $data = jsonLoad();
    foreach ($data['orders'] as &$o) {
        if ((int)$o['id'] === $id) {
            $o['payment_method'] = $method;
            $o['payment_reference'] = $reference;
            $o['receipt_file'] = $receiptFile;
            break;
        }
    }
    unset($o);
    jsonSave($data);
}

function db_createSupportTicket($name, $phone, $subject, $message) {
    if (useSqlite()) {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO support_tickets (name, phone, subject, message, status) VALUES (?,?,?,?, 'open')");
        $stmt->execute([$name, $phone, $subject, $message]);
        return (int)$pdo->lastInsertId();
    }
    $data = jsonLoad();
    if (!isset($data['support_tickets'])) $data['support_tickets'] = [];
    if (!isset($data['next_support_id'])) $data['next_support_id'] = 1;
    $id = $data['next_support_id']++;
    $data['support_tickets'][] = ['id'=>$id,'name'=>$name,'phone'=>$phone,'subject'=>$subject,'message'=>$message,'status'=>'open','created_at'=>date('Y-m-d H:i:s')];
    jsonSave($data);
    return $id;
}

function db_getSupportTickets($statusFilter = '') {
    if (useSqlite()) {
        if ($statusFilter !== '') {
            $stmt = getDB()->prepare("SELECT * FROM support_tickets WHERE status=? ORDER BY created_at DESC");
            $stmt->execute([$statusFilter]);
            return $stmt->fetchAll();
        }
        return getDB()->query("SELECT * FROM support_tickets ORDER BY created_at DESC")->fetchAll();
    }
    $data = jsonLoad();
    $list = $data['support_tickets'] ?? [];
    if ($statusFilter !== '') $list = array_filter($list, fn($t) => ($t['status'] ?? '') === $statusFilter);
    usort($list, fn($a,$b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
    return array_values($list);
}

function db_getSupportTicket($id) {
    $id=(int)$id;
    if (useSqlite()) { $st=getDB()->prepare("SELECT * FROM support_tickets WHERE id=?"); $st->execute([$id]); return $st->fetch() ?: null; }
    foreach ((jsonLoad()['support_tickets'] ?? []) as $t) if ((int)$t['id']===$id) return $t;
    return null;
}

function db_updateSupportStatus($id, $status) {
    $id=(int)$id; if (!in_array($status,['open','answered','closed'],true)) return;
    if (useSqlite()) { getDB()->prepare("UPDATE support_tickets SET status=? WHERE id=?")->execute([$status,$id]); return; }
    $data=jsonLoad(); foreach ($data['support_tickets'] as &$t) if ((int)$t['id']===$id) { $t['status']=$status; break; } unset($t); jsonSave($data);
}

function db_updateOrderStatus($id, $status) {
    $id = (int)$id;
    $allowed = ['pending', 'confirmed', 'shipped', 'cancelled'];
    if (!in_array($status, $allowed)) return;
    if (useSqlite()) {
        getDB()->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status, $id]);
        return;
    }
    $data = jsonLoad();
    foreach ($data['orders'] as &$o) {
        if ((int)$o['id'] === $id) {
            $o['status'] = $status;
            break;
        }
    }
    unset($o);
    jsonSave($data);
}

function db_stats() {
    if (useSqlite()) {
        $pdo = getDB();
        return [
            'products' => (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'pending' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(),
            'revenue' => (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('confirmed','shipped')")->fetchColumn(),
        ];
    }
    $data = jsonLoad();
    $pending = 0; $revenue = 0;
    foreach ($data['orders'] as $o) {
        if (($o['status'] ?? '') === 'pending') $pending++;
        if (in_array($o['status'] ?? '', ['confirmed', 'shipped'])) $revenue += (float)$o['total'];
    }
    return [
        'products' => count($data['products']),
        'orders' => count($data['orders']),
        'pending' => $pending,
        'revenue' => $revenue,
    ];
}
?>
