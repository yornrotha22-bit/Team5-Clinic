<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config/db.php';

if (file_exists('../../middleware/auth.php')) {
    require_once '../../middleware/auth.php';
}

$error = '';

// Handling Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $error = 'សូមបញ្ចូលឈ្មោះ Department!';
    } else {
        try {
            $sql = "INSERT INTO departments (name, description) VALUES (:name, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':description' => $description
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Database Error: ' . $e->getMessage();
        }
    }
}

if (file_exists('../../includes/header.php')) {
    include '../../includes/header.php';
}
include '../../includes/sidebar.php';
?>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  /* Base Wrapper adjustment */
  .create-page-container {
    margin-left: 260px;
    padding: 32px 40px;
    background-color: #f8fafc;
    min-height: calc(100vh - 70px);
    box-sizing: border-box;
  }

  /* Page Header Title */
  .page-header-title {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .back-link {
    color: #64748b;
    font-size: 18px;
    text-decoration: none;
    transition: color 0.2s;
  }
  .back-link:hover {
    color: #2563eb;
  }

  /* Form Card Styling */
  .create-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 32px 36px;
    width: 100%;
    max-width: 580px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    border: 1px solid #f1f5f9;
  }

  .form-label-custom {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 8px;
    display: block;
  }

  .form-control-custom {
    width: 100%;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    color: #0f172a;
    box-sizing: border-box;
    transition: all 0.2s ease;
  }

  .form-control-custom:focus {
    background-color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    outline: none;
  }

  .form-control-custom::placeholder {
    color: #94a3b8;
  }

  .form-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
  }

  .btn-create {
    background-color: #2563eb !important;
    color: #ffffff !important;
    padding: 10px 24px !important;
    border-radius: 20px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    border: none !important;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    transition: all 0.2s ease;
  }

  .btn-create:hover {
    background-color: #1d4ed8 !important;
  }

  .btn-cancel {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    padding: 10px 24px !important;
    border-radius: 20px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    text-decoration: none !important;
    display: inline-block;
    transition: all 0.2s ease;
  }

  .btn-cancel:hover {
    background-color: #e2e8f0 !important;
    color: #0f172a !important;
  }
</style>

<div class="create-page-container">

  <!-- Header Title with Back Arrow -->
  <div class="page-header-title">
    <a href="index.php" class="back-link" title="Back to Departments">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
    <span>Add New Department</span>
  </div>

  <!-- Form Card -->
  <div class="create-card">
    
    <?php if (!empty($error)): ?>
      <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 20px;">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST">
      
      <!-- Department Name -->
      <div style="margin-bottom: 20px;">
        <label class="form-label-custom">DEPARTMENT NAME *</label>
        <input 
          type="text" 
          name="name" 
          class="form-control-custom" 
          placeholder="e.g. Cardiology" 
          required
        >
      </div>

      <!-- Description -->
      <div style="margin-bottom: 10px;">
        <label class="form-label-custom">DESCRIPTION</label>
        <textarea 
          name="description" 
          rows="4" 
          class="form-control-custom" 
          placeholder="Brief description of this department and its services..."
        ></textarea>
      </div>

      <!-- Action Buttons -->
      <div class="form-footer">
        <button type="submit" class="btn-create">Create Department</button>
        <a href="index.php" class="btn-cancel">Cancel</a>
      </div>

    </form>

  </div>

</div>

<?php 
if (file_exists('../../includes/footer.php')) {
    include '../../includes/footer.php'; 
}
?>