<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../config/db.php';
if (file_exists('../../middleware/auth.php')) {
    require_once '../../middleware/auth.php';
}
try {
    $sql = "SELECT d.id, d.name, d.description, COUNT(doc.id) AS total_doctors 
            FROM departments d 
            LEFT JOIN doctors doc ON d.id = doc.department_id 
            GROUP BY d.id, d.name, d.description 
            ORDER BY d.id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<h2 style='color:red;'>Database Query Error: " . $e->getMessage() . "</h2>");
}
if (file_exists('../../includes/header.php')) {
    include '../../includes/header.php';
}
include '../../includes/sidebar.php';
?>

<!-- CDNs -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
  .main-wrapper {
    margin-left: 260px;
    min-height: calc(100vh - 60px);
    background-color: #f8fafc;
    padding: 32px 40px !important;
  }

  /* Page Header Clean & Balanced */
  .page-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    padding:60px 10px;
  }

  .page-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.3px;
  }

  .btn-add-dept {
    background-color: #2563eb !important;
    color: #ffffff !important;
    padding: 10px 22px !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    transition: all 0.2s ease;
  }
  
  .btn-add-dept:hover {
    background-color: #1d4ed8 !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
  }

  /* Improved Card Design */
  .dept-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: all 0.25s ease-in-out;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .dept-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.07);
    border-color: #e2e8f0;
  }

  .dept-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }

  .dept-code { 
    font-size: 12px; 
    font-weight: 700; 
    color: #94a3b8; 
    letter-spacing: 0.5px;
    margin-top: 12px;
  }

  .dept-title { 
    font-size: 18px; 
    font-weight: 700; 
    color: #0f172a; 
    margin-top: 2px;
    margin-bottom: 6px;
  }

  .dept-desc { 
    font-size: 13px; 
    color: #64748b; 
    line-height: 1.5;
    margin-bottom: 20px; 
    min-height: 38px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .doctor-badge { 
    display: inline-flex; 
    align-items: center; 
    gap: 6px; 
    padding: 6px 14px; 
    border-radius: 30px; 
    font-size: 12px; 
    font-weight: 600; 
  }

  .action-btn { 
    color: #94a3b8; 
    transition: all 0.2s; 
    font-size: 15px; 
    padding: 6px;
    border-radius: 8px;
    text-decoration: none; 
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .action-btn:hover { 
    color: #2563eb; 
    background-color: #eff6ff;
  }

  .action-btn-delete:hover { 
    color: #ef4444; 
    background-color: #fef2f2;
  }

  /* Empty State UI */
  .empty-state-box {
    background: #ffffff;
    border-radius: 20px;
    padding: 60px 20px;
    text-align: center;
    border: 1px dashed #cbd5e1;
  }
</style>

<div class="main-wrapper">
  <div class="container-fluid p-0">
    
    <!-- Page Header -->
    <div class="page-header-custom">
      <h3 class="page-title"></h3>
      <a href="create.php" class="btn-add-dept">
        <i class="fa-solid fa-plus"></i> Add Department
      </a>
    </div>

    <!-- Cards Grid -->
    <div class="row g-4">
      <?php 
      $themeStyles = [
        ['bg' => '#eff6ff', 'text' => '#2563eb', 'icon' => 'fa-heart-pulse'],
        ['bg' => '#f0fdf4', 'text' => '#16a34a', 'icon' => 'fa-brain'],
        ['bg' => '#faf5ff', 'text' => '#9333ea', 'icon' => 'fa-bone'],
        ['bg' => '#fffbe1', 'text' => '#d97706', 'icon' => 'fa-baby'],
        ['bg' => '#fdf2f8', 'text' => '#db2777', 'icon' => 'fa-hand-dots'],
        ['bg' => '#f0fdfa', 'text' => '#0d9488', 'icon' => 'fa-notes-medical'],
      ];

      if (!empty($departments)):
        foreach ($departments as $index => $dept):
          $theme = $themeStyles[$index % count($themeStyles)];
          $deptCode = "D" . str_pad($dept['id'], 3, "0", STR_PAD_LEFT);
      ?>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
          <div class="dept-card">
            
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="dept-icon-box" style="background-color: <?= $theme['bg'] ?>; color: <?= $theme['text'] ?>;">
                  <i class="fa-solid <?= $theme['icon'] ?>"></i>
                </div>
                <div class="d-flex gap-1">
                  <a href="edit.php?id=<?= $dept['id'] ?>" class="action-btn" title="Edit">
                    <i class="fa-regular fa-pen-to-square"></i>
                  </a>
                  <a href="delete.php?id=<?= $dept['id'] ?>" 
                     class="action-btn action-btn-delete" 
                     onclick="return confirm('តើអ្នកប្រាកដថាចង់លុប Department «<?= htmlspecialchars($dept['name']) ?>» នេះមែនទេ?')" 
                     title="Delete">
                    <i class="fa-regular fa-trash-can"></i>
                  </a>
                </div>
              </div>

              <div class="dept-code"><?= $deptCode ?></div>
              <div class="dept-title"><?= htmlspecialchars($dept['name']) ?></div>
              <div class="dept-desc">
                <?= htmlspecialchars($dept['description'] ?? 'No description available.') ?>
              </div>
            </div>

            <div>
              <span class="doctor-badge" style="background-color: <?= $theme['bg'] ?>; color: <?= $theme['text'] ?>;">
                <i class="fa-solid fa-stethoscope"></i>
                <?= $dept['total_doctors'] ?> <?= $dept['total_doctors'] == 1 ? 'doctor' : 'doctors' ?>
              </span>
            </div>

          </div>
        </div>
      <?php 
        endforeach;
      else: 
      ?>
        <div class="col-12">
          <div class="empty-state-box">
            <i class="fa-solid fa-hospital-user text-muted mb-3" style="font-size: 48px;"></i>
            <h5 class="fw-bold text-dark mb-1">មិនទាន់មានទិន្នន័យ Department</h5>
            <p class="text-muted small mb-3">សូមចុចប៊ូតុងខាងក្រោមដើម្បីបន្ថែម Department ថ្មីចូលក្នុងប្រព័ន្ធ។</p>
            <a href="create.php" class="btn-add-dept">
              <i class="fa-solid fa-plus"></i> Add Department
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php 
if (file_exists('../../includes/footer.php')) {
    include '../../includes/footer.php'; 
}
?>