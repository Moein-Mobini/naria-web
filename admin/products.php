<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

$message = '';
$error = '';

if (isset($_GET['delete'])) {
    db_deleteProduct((int)$_GET['delete']);
    header('Location: products.php?msg=deleted');
    exit;
}

if (isset($_GET['toggle'])) {
    $field = ($_GET['field'] ?? '') === 'featured' ? 'featured' : 'active';
    db_toggleProduct((int)$_GET['toggle'], $field);
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? 'تنباکو');
    $stock = (int)($_POST['stock'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $active = isset($_POST['active']) ? 1 : 0;

    if ($name === '' || $price < 0) {
        $error = 'نام و قیمت معتبر الزامی است.';
    } else {
        $imageName = '';
        if ($id > 0) {
            $old = db_getProduct($id);
            $imageName = $old['image'] ?? '';
        }

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if (in_array($ext, $allowed)) {
                $imageName = 'p_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $imageName);
                if ($id > 0 && !empty($old['image']) && file_exists(UPLOAD_DIR . $old['image'])) {
                    @unlink(UPLOAD_DIR . $old['image']);
                }
            }
        }

        db_saveProduct([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'image' => $imageName,
            'stock' => $stock,
            'featured' => $featured,
            'active' => $active,
        ], $id);

        $message = $id > 0 ? 'محصول به‌روزرسانی شد.' : 'محصول جدید اضافه شد.';
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') $message = 'محصول حذف شد.';

$products = db_getProducts(false, false);
$edit = null;
if (isset($_GET['edit'])) {
    $edit = db_getProduct((int)$_GET['edit']);
}

$categories = ['تنباکو', 'سیگار', 'هوکا', 'ویپ و پاد'];

$adminTitle = 'مدیریت محصولات';
$adminActive = 'products';
include __DIR__ . '/layout.php';
?>

<div class="admin-header">
  <h1>محصولات</h1>
  <button class="btn sm" onclick="document.getElementById('productForm').style.display='block'; window.scrollTo(0,0)">+ محصول جدید</button>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div id="productForm" style="<?= $edit ? '' : 'display:none' ?>;background:linear-gradient(145deg,#151515,#0b0b0b);border:1px solid var(--line);border-radius:18px;padding:28px;margin-bottom:30px">
  <h2 style="margin-top:0;color:var(--gold2)"><?= $edit ? 'ویرایش محصول' : 'افزودن محصول جدید' ?></h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
      <div class="form-group">
        <label>نام محصول *</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>دسته‌بندی</label>
        <select name="category">
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c ?>" <?= ($edit['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>قیمت (تومان) *</label>
        <input type="number" name="price" required min="0" step="1000" value="<?= $edit['price'] ?? '' ?>">
      </div>
      <div class="form-group">
        <label>موجودی</label>
        <input type="number" name="stock" min="0" value="<?= $edit['stock'] ?? 50 ?>">
      </div>
    </div>
    <div class="form-group">
      <label>توضیحات</label>
      <textarea name="description" rows="3"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>تصویر محصول</label>
      <input type="file" name="image" accept="image/*">
      <?php if (!empty($edit['image'])): ?>
        <img src="../assets/uploads/<?= htmlspecialchars($edit['image']) ?>" class="img-preview" alt="">
      <?php endif; ?>
    </div>
    <div style="display:flex;gap:20px;margin:12px 0">
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
        <input type="checkbox" name="featured" <?= !empty($edit['featured']) ? 'checked' : '' ?>> ویژه (نمایش در صفحه اصلی)
      </label>
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
        <input type="checkbox" name="active" <?= ($edit === null || !empty($edit['active'])) ? 'checked' : '' ?>> فعال
      </label>
    </div>
    <button type="submit" class="btn"><?= $edit ? 'ذخیره تغییرات' : 'افزودن محصول' ?></button>
    <?php if ($edit): ?>
      <a href="products.php" class="btn ghost">انصراف</a>
    <?php else: ?>
      <button type="button" class="btn ghost" onclick="document.getElementById('productForm').style.display='none'">بستن</button>
    <?php endif; ?>
  </form>
</div>

<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>تصویر</th>
        <th>نام</th>
        <th>دسته</th>
        <th>قیمت</th>
        <th>موجودی</th>
        <th>ویژه</th>
        <th>وضعیت</th>
        <th>عملیات</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td>
          <?php if (!empty($p['image']) && file_exists(UPLOAD_DIR . $p['image'])): ?>
            <img src="../assets/uploads/<?= htmlspecialchars($p['image']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px">
          <?php else: ?>
            <span style="color:var(--gold2)">◈</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['category']) ?></td>
        <td><?= number_format($p['price']) ?></td>
        <td><?= $p['stock'] ?></td>
        <td>
          <a href="?toggle=<?= $p['id'] ?>&field=featured" style="color:<?= !empty($p['featured']) ? 'var(--gold2)' : 'var(--muted)' ?>">
            <?= !empty($p['featured']) ? '★' : '☆' ?>
          </a>
        </td>
        <td>
          <a href="?toggle=<?= $p['id'] ?>&field=active">
            <span class="badge <?= !empty($p['active']) ? 'badge-confirmed' : 'badge-cancelled' ?>">
              <?= !empty($p['active']) ? 'فعال' : 'غیرفعال' ?>
            </span>
          </a>
        </td>
        <td style="white-space:nowrap">
          <a href="?edit=<?= $p['id'] ?>" class="btn sm">ویرایش</a>
          <a href="?delete=<?= $p['id'] ?>" class="btn sm danger" onclick="return confirm('حذف این محصول؟')">حذف</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</main></div></body></html>
