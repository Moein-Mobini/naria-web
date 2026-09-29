<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$action = $input['action'] ?? '';

function cartCount() {
    $c = 0;
    foreach ($_SESSION['cart'] as $item) $c += $item['qty'];
    return $c;
}

function cartTotal() {
    $t = 0;
    foreach ($_SESSION['cart'] as $item) $t += $item['price'] * $item['qty'];
    return $t;
}

switch ($action) {
    case 'add':
        $id = (int)($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        $price = (float)($input['price'] ?? 0);
        $qty = max(1, (int)($input['qty'] ?? 1));
        if ($id <= 0 || $name === '') {
            echo json_encode(['ok' => false, 'error' => 'داده نامعتبر']);
            exit;
        }
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'qty' => $qty
            ];
        }
        echo json_encode(['ok' => true, 'count' => cartCount(), 'total' => cartTotal()]);
        break;

    case 'update':
        $id = (int)($input['id'] ?? 0);
        $qty = (int)($input['qty'] ?? 0);
        if (isset($_SESSION['cart'][$id])) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id]['qty'] = $qty;
            }
        }
        echo json_encode(['ok' => true, 'count' => cartCount(), 'total' => cartTotal()]);
        break;

    case 'remove':
        $id = (int)($input['id'] ?? 0);
        unset($_SESSION['cart'][$id]);
        echo json_encode(['ok' => true, 'count' => cartCount(), 'total' => cartTotal()]);
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['ok' => true, 'count' => 0, 'total' => 0]);
        break;

    case 'get':
        echo json_encode([
            'ok' => true,
            'items' => array_values($_SESSION['cart']),
            'count' => cartCount(),
            'total' => cartTotal()
        ]);
        break;

    default:
        echo json_encode(['ok' => false, 'error' => 'عملیات نامعتبر']);
}
?>
