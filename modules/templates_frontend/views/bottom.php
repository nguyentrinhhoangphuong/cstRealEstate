</div><!-- /#page-content -->

<!-- ─── Footer ──────────────────────────────────── -->
<footer style="background:var(--brand-dark);color:rgba(255,255,255,0.65);
               font-family:var(--font-body);font-weight:300;">

  <!-- Main footer -->
  <div class="container py-5">
    <div class="row g-5">

      <!-- Col 1: Brand -->
      <div class="col-lg-4">
        <a href="<?= BASE_URL ?>" class="d-inline-block mb-3"
           style="font-family:var(--font-display);font-size:1.8rem;
                  font-weight:600;color:#fff;letter-spacing:0.02em;">
          Prop<span style="color:var(--brand-gold);">Viet</span>
        </a>
        <p style="font-size:0.82rem;line-height:1.8;color:rgba(255,255,255,0.5);
                  max-width:300px;">
          Nền tảng bất động sản cao cấp hàng đầu Việt Nam. Kết nối người mua,
          người bán và nhà đầu tư trên toàn quốc.
        </p>
        <!-- Socials -->
        <div class="d-flex gap-3 mt-4">
          <?php
            $socials = [
              ['icon'=>'bi-facebook',  'href'=>'#', 'label'=>'Facebook'],
              ['icon'=>'bi-youtube',   'href'=>'#', 'label'=>'YouTube'],
              ['icon'=>'bi-tiktok',    'href'=>'#', 'label'=>'TikTok'],
              ['icon'=>'bi-linkedin',  'href'=>'#', 'label'=>'LinkedIn'],
            ];
            foreach ($socials as $s):
          ?>
            <a href="<?= $s['href'] ?>" aria-label="<?= $s['label'] ?>"
               style="width:36px;height:36px;border:1px solid rgba(201,169,110,0.3);
                      border-radius:50%;display:flex;align-items:center;
                      justify-content:center;color:rgba(255,255,255,0.5);
                      font-size:0.85rem;transition:all 0.2s;"
               onmouseover="this.style.borderColor='var(--brand-gold)';this.style.color='var(--brand-gold)'"
               onmouseout="this.style.borderColor='rgba(201,169,110,0.3)';this.style.color='rgba(255,255,255,0.5)'">
              <i class="bi <?= $s['icon'] ?>"></i>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Col 2: Bất động sản -->
      <div class="col-6 col-lg-2">
        <h6 style="font-size:0.68rem;font-weight:400;letter-spacing:0.18em;
                   text-transform:uppercase;color:var(--brand-gold);margin-bottom:1.2rem;">
          Bất động sản
        </h6>
        <ul class="list-unstyled mb-0" style="font-size:0.82rem;">
          <?php
            $links = [
              ['Căn hộ',    'listings/type/can-ho'],
              ['Biệt thự',  'listings/type/biet-thu'],
              ['Đất nền',   'listings/type/dat-nen'],
              ['Thương mại','listings/type/thuong-mai'],
              ['Cho thuê',  'listings/status/for-rent'],
            ];
            foreach ($links as [$label, $path]):
          ?>
            <li class="mb-2">
              <a href="<?= BASE_URL . $path ?>"
                 style="color:rgba(255,255,255,0.5);transition:color 0.2s;"
                 onmouseover="this.style.color='var(--brand-gold)'"
                 onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                <?= $label ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Col 3: Công ty -->
      <div class="col-6 col-lg-2">
        <h6 style="font-size:0.68rem;font-weight:400;letter-spacing:0.18em;
                   text-transform:uppercase;color:var(--brand-gold);margin-bottom:1.2rem;">
          Công ty
        </h6>
        <ul class="list-unstyled mb-0" style="font-size:0.82rem;">
          <?php
            $links2 = [
              ['Về chúng tôi', 'about'],
              ['Agents',       'agents'],
              ['Tin tức',      'blog'],
              ['Bản đồ',       'map'],
              ['Liên hệ',      'contact'],
            ];
            foreach ($links2 as [$label, $path]):
          ?>
            <li class="mb-2">
              <a href="<?= BASE_URL . $path ?>"
                 style="color:rgba(255,255,255,0.5);transition:color 0.2s;"
                 onmouseover="this.style.color='var(--brand-gold)'"
                 onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                <?= $label ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Col 4: Liên hệ -->
      <div class="col-lg-4">
        <h6 style="font-size:0.68rem;font-weight:400;letter-spacing:0.18em;
                   text-transform:uppercase;color:var(--brand-gold);margin-bottom:1.2rem;">
          Liên hệ
        </h6>
        <ul class="list-unstyled mb-0" style="font-size:0.82rem;">
          <li class="d-flex gap-2 mb-3" style="color:rgba(255,255,255,0.5);">
            <i class="bi bi-geo-alt-fill mt-1" style="color:var(--brand-gold);flex-shrink:0;"></i>
            <span>72 Lê Thánh Tôn, Bến Nghé, Quận 1, TP. Hồ Chí Minh</span>
          </li>
          <li class="mb-2">
            <a href="tel:+84901234567" style="color:rgba(255,255,255,0.5);"
               onmouseover="this.style.color='var(--brand-gold)'"
               onmouseout="this.style.color='rgba(255,255,255,0.5)'">
              <i class="bi bi-telephone me-2" style="color:var(--brand-gold);"></i>
              0901 234 567
            </a>
          </li>
          <li class="mb-4">
            <a href="mailto:hello@propviet.vn" style="color:rgba(255,255,255,0.5);"
               onmouseover="this.style.color='var(--brand-gold)'"
               onmouseout="this.style.color='rgba(255,255,255,0.5)'">
              <i class="bi bi-envelope me-2" style="color:var(--brand-gold);"></i>
              hello@propviet.vn
            </a>
          </li>
        </ul>

        <!-- Newsletter mini -->
        <div style="border:1px solid rgba(201,169,110,0.2);padding:1rem;">
          <p style="font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;
                    color:var(--brand-gold);margin-bottom:0.6rem;">
            Nhận tin mới nhất
          </p>
          <div class="d-flex gap-0">
            <input type="email" placeholder="Email của bạn"
                   style="flex:1;background:rgba(255,255,255,0.05);
                          border:1px solid rgba(255,255,255,0.12);border-right:none;
                          color:#fff;padding:0.45rem 0.75rem;font-size:0.8rem;
                          font-family:var(--font-body);outline:none;border-radius:0;">
            <button style="background:var(--brand-gold);border:none;color:var(--brand-dark);
                           padding:0.45rem 1rem;font-size:0.75rem;letter-spacing:0.1em;
                           text-transform:uppercase;cursor:pointer;border-radius:0;
                           font-family:var(--font-body);transition:background 0.2s;"
                    onmouseover="this.style.background='var(--brand-gold-lt)'"
                    onmouseout="this.style.background='var(--brand-gold)'">
              Đăng ký
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bottom bar -->
  <div style="border-top:1px solid rgba(201,169,110,0.12);">
    <div class="container py-3 d-flex flex-column flex-md-row
                justify-content-between align-items-center gap-2">
      <p style="font-size:0.72rem;color:rgba(255,255,255,0.3);margin:0;
                letter-spacing:0.04em;">
        &copy; <?= date('Y') ?> PropViet. Bảo lưu mọi quyền.
      </p>
      <div class="d-flex gap-3" style="font-size:0.72rem;">
        <a href="<?= BASE_URL ?>privacy" style="color:rgba(255,255,255,0.3);"
           onmouseover="this.style.color='var(--brand-gold)'"
           onmouseout="this.style.color='rgba(255,255,255,0.3)'">
          Chính sách bảo mật
        </a>
        <a href="<?= BASE_URL ?>terms" style="color:rgba(255,255,255,0.3);"
           onmouseover="this.style.color='var(--brand-gold)'"
           onmouseout="this.style.color='rgba(255,255,255,0.3)'">
          Điều khoản sử dụng
        </a>
      </div>
    </div>
  </div>

</footer>
<!-- /Footer -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($extra_js)): ?>
  <?= $extra_js ?>
<?php endif; ?>
</body>
</html>