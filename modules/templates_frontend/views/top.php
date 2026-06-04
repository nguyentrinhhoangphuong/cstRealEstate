
<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?? 'PropViet — Bất Động Sản' ?></title>
  <meta name="description" content="<?= $meta_desc ?? 'Nền tảng bất động sản hàng đầu Việt Nam' ?>">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <!-- CSS -->
  <link rel="stylesheet" href="templates_frontend_module/css/style.css">


  <?php if (!empty($extra_css)): ?>
    <?= $extra_css ?>
  <?php endif; ?>
</head>
<body>

<!-- ─── Top Bar ─────────────────────────────────── -->
<div class="top-bar d-none d-lg-block" style="position:fixed;top:0;left:0;right:0;z-index:1031;">
  <!-- Nếu dùng top-bar, đổi --nav-h sang 100px, hiện tại ẩn để đơn giản -->
</div>

<!-- ─── Navbar ──────────────────────────────────── -->
<nav id="main-nav" class="navbar navbar-expand-lg">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand" href="<?= BASE_URL ?>">
      Prop<span>Viet</span>
      <span class="brand-tagline">Bất động sản cao cấp</span>
    </a>

    <!-- Mobile: icons + toggler -->
    <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-2">
      <button class="nav-icon-btn" title="Tìm kiếm" aria-label="Tìm kiếm">
        <i class="bi bi-search"></i>
      </button>
    </div>
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#mainNavCollapse"
            aria-controls="mainNavCollapse" aria-expanded="false" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Links -->
    <div class="collapse navbar-collapse" id="mainNavCollapse">
      <ul class="navbar-nav mx-auto gap-1">

        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>">Trang chủ</a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">Bất động sản</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>listings">Tất cả dự án</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>listings/type/can-ho">Căn hộ</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>listings/type/biet-thu">Biệt thự</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>listings/type/dat-nen">Đất nền</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>listings/type/thuong-mai">Thương mại</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>map">Bản đồ</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>agents">Agents</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>blog">Tin tức</a>
        </li>

      </ul>

      <!-- Right side -->
      <div class="d-flex align-items-center gap-3">
        <button class="nav-icon-btn d-none d-lg-inline" title="Tìm kiếm" aria-label="Tìm kiếm">
          <i class="bi bi-search"></i>
        </button>

<a href="<?= BASE_URL ?>account/login"
   style="font-size:0.78rem;letter-spacing:0.08em;text-transform:uppercase;
          color:rgba(255,255,255,0.7);">
  Đăng nhập
</a>
<a href="<?= BASE_URL ?>account/register" class="btn-nav-cta">Đăng tin</a>
      </div>
    </div>

  </div>
</nav>
<!-- /Navbar -->

<!-- ─── Search Overlay (toggle bằng JS) ──────────── -->
<div id="search-overlay"
     style="display:none;position:fixed;inset:0;background:rgba(15,25,35,0.97);
            z-index:2000;align-items:center;justify-content:center;">
  <div style="width:min(640px,90%);padding:2rem;">
    <div class="d-flex align-items-center border-bottom border-secondary pb-3 mb-3">
      <i class="bi bi-search text-gold me-3" style="font-size:1.4rem;"></i>
      <input id="search-input" type="text"
             placeholder="Tìm kiếm bất động sản..."
             style="background:none;border:none;outline:none;
                    font-family:var(--font-body);font-size:1.2rem;
                    color:#fff;width:100%;"
             autocomplete="off">
      <button onclick="closeSearch()"
              style="background:none;border:none;color:rgba(255,255,255,0.5);
                     font-size:1.5rem;cursor:pointer;padding:0;">
        <i class="bi bi-x"></i>
      </button>
    </div>
    <p style="font-size:0.75rem;letter-spacing:0.1em;
              text-transform:uppercase;color:rgba(255,255,255,0.3);">
      Gợi ý: căn hộ quận 7, biệt thự thủ đức, đất nền bình dương...
    </p>
  </div>
</div>

<!-- ─── Page Content ─────────────────────────────── -->
<div id="page-content">

<script src="templates_frontend_module/js/main.js"></script>