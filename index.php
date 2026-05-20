<?php
/**
 * Halaman Utama — Pusaka Himatif
 * Menampilkan section artikel dinamis dari database
 */

require_once __DIR__ . '/config/database.php';

// Ambil 6 artikel published terbaru
try {
    $db = getDB();
    $artikels = $db->query(
        "SELECT id, judul, slug, ringkasan, gambar, created_at
         FROM artikel
         WHERE status = 'published'
         ORDER BY created_at DESC
         LIMIT 6"
    )->fetchAll();
} catch (Exception $e) {
    $artikels = [];
}

function formatTanggal(string $date): string {
    $bulan = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $d = date('j', strtotime($date));
    $m = (int) date('n', strtotime($date));
    $y = date('Y', strtotime($date));
    return "$d {$bulan[$m]} $y";
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Pusaka Himatif — Solusi percetakan terpercaya untuk mahasiswa. Print, fotocopy, scan, jilid, dan laminating di area kampus." />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="Assets/logo.png" type="image/x-icon" />
    <title>Pusaka Himatif</title>
  </head>

  <body>
    <header>
      <nav>
        <a href="#" class="logo">Pusaka Himatif<span>.</span></a>
        <div class="menuToggle" onclick="toggleMenu();"></div>
        <ul class="navigation">
          <li><a href="#banner"       onclick="toggleMenu();">Beranda</a></li>
          <li><a href="#order"        onclick="toggleMenu();">Pesan</a></li>
          <li><a href="#calculator"   onclick="toggleMenu();">Kalkulator</a></li>
          <li><a href="#activity"     onclick="toggleMenu();">Aktivitas</a></li>
          <li><a href="#artikel"      onclick="toggleMenu();">Artikel</a></li>
          <li><a href="#about"        onclick="toggleMenu();">Tentang</a></li>
          <li><a href="#testimonials" onclick="toggleMenu();">Ulasan</a></li>
          <li><a href="#contact"      onclick="toggleMenu();">Kontak</a></li>
        </ul>
      </nav>
    </header>

    <main>
      <!-- ===== BANNER ===== -->
      <article class="banner" id="banner">
        <div class="content">
          <h2>Siap Membantu</h2>
          <p>
            Pusaka Himatif adalah sebuah tempat yang menyediakan berbagai
            kebutuhan mahasiswa seperti Print, Fotocopy, Scan dan berbagai
            kebutuhan mahasiswa, Dengan jam kerja mulai 08.00 - 19.00
            dari hari senin sampai jumat.
          </p>
          <a href="#" class="btn">Temukan disini</a>
        </div>
      </article>

      <!-- ===== PESAN ===== -->
      <article class="order" id="order">
        <div class="title">
          <h2 class="titleText"><span>P</span>esan Disini</h2>
          <p>Segera hubungi kami jika ingin memesan</p>
        </div>
        <div class="orderForm">
          <div class="content">
            <div class="box">
              <div class="imgBx">
                <img src="Assets/barcode.jpg" alt="" height="20px" />
              </div>
              <h3>Kirim Pesan</h3>
              <a
                href="https://wa.me/message/TOWRXODYCBVYB1"
                target="_blank"
                rel="nofollow"
                title="Menuju WhatsApp"
                class="inputBox"
              >
                Pesan
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- ===== KALKULATOR HARGA ===== -->
      <article class="calculator" id="calculator">
        <div class="title">
          <h2 class="titleText"><span>K</span>alkulator Harga</h2>
          <p>Estimasi biaya layanan Pusaka Himatif secara instan &amp; akurat</p>
        </div>

        <div class="calcWrapper">
          <!-- Step 1 -->
          <div class="calcStep">
            <h3 class="stepLabel"><span class="stepNum">1</span> Pilih Layanan</h3>
            <div class="serviceCards">
              <div class="serviceCard active" onclick="selectService(this, 'print')">
                <div class="serviceIcon">🖨️</div>
                <span>Print</span>
              </div>
              <div class="serviceCard" onclick="selectService(this, 'fotocopy')">
                <div class="serviceIcon">📄</div>
                <span>Fotocopy</span>
              </div>
              <div class="serviceCard" onclick="selectService(this, 'scan')">
                <div class="serviceIcon">🔍</div>
                <span>Scan</span>
              </div>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="calcStep">
            <h3 class="stepLabel"><span class="stepNum">2</span> Ukuran Kertas</h3>
            <div class="toggleGroup" id="sizeGroup">
              <button class="toggleBtn active" onclick="selectToggle(this, 'size', 'a4')">A4</button>
              <button class="toggleBtn" onclick="selectToggle(this, 'size', 'f4')">F4</button>
              <button class="toggleBtn" onclick="selectToggle(this, 'size', 'a3')">A3</button>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="calcStep" id="colorStep">
            <h3 class="stepLabel"><span class="stepNum">3</span> Mode Cetak</h3>
            <div class="toggleGroup" id="colorGroup">
              <button class="toggleBtn active" onclick="selectToggle(this, 'colorMode', 'bw')">⬛ Hitam Putih</button>
              <button class="toggleBtn" onclick="selectToggle(this, 'colorMode', 'color')">🎨 Berwarna</button>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="calcStep">
            <h3 class="stepLabel"><span class="stepNum">4</span> Detail</h3>
            <div class="inputRow">
              <div class="inputGroup">
                <label for="pages">Jumlah Halaman</label>
                <div class="numberInput">
                  <button onclick="adjustValue('pages', -1)" aria-label="Kurangi halaman">−</button>
                  <input type="number" id="pages" value="1" min="1" onchange="calculate()" aria-label="Jumlah halaman" />
                  <button onclick="adjustValue('pages', 1)" aria-label="Tambah halaman">+</button>
                </div>
              </div>
              <div class="inputGroup">
                <label for="copies">Jumlah Rangkap</label>
                <div class="numberInput">
                  <button onclick="adjustValue('copies', -1)" aria-label="Kurangi rangkap">−</button>
                  <input type="number" id="copies" value="1" min="1" onchange="calculate()" aria-label="Jumlah rangkap" />
                  <button onclick="adjustValue('copies', 1)" aria-label="Tambah rangkap">+</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 5 -->
          <div class="calcStep">
            <h3 class="stepLabel"><span class="stepNum">5</span> Finishing <span class="optional">(Opsional)</span></h3>
            <div class="checkboxGroup">
              <label class="checkItem" id="label-jilid-spiral">
                <input type="checkbox" id="jilid_spiral" onchange="onJilid('jilid_spiral')" />
                <span>Jilid Spiral</span>
                <span class="price-tag">+ Rp 5.000 / rangkap</span>
              </label>
              <label class="checkItem" id="label-jilid-soft">
                <input type="checkbox" id="jilid_soft" onchange="onJilid('jilid_soft')" />
                <span>Jilid Softcover</span>
                <span class="price-tag">+ Rp 10.000 / rangkap</span>
              </label>
              <label class="checkItem">
                <input type="checkbox" id="laminating" onchange="calculate()" />
                <span>Laminating</span>
                <span class="price-tag">+ Rp 2.000 / lembar</span>
              </label>
            </div>
          </div>

          <!-- Hasil -->
          <div class="calcResult">
            <div class="resultHeader">
              <span class="resultIcon">💰</span>
              <h3>Estimasi Biaya</h3>
            </div>
            <div class="resultBreakdown" id="resultBreakdown"></div>
            <div class="resultTotal">
              <span class="totalLabel">Total Estimasi</span>
              <span class="totalAmount" id="totalAmount">Rp 0</span>
            </div>
            <p class="resultNote">* Harga merupakan estimasi. Hubungi kami untuk konfirmasi harga pasti.</p>
            <a
              href="https://wa.me/message/TOWRXODYCBVYB1"
              target="_blank"
              rel="nofollow"
              title="Pesan via WhatsApp"
              class="btn calcOrderBtn"
            >
              📱 Pesan Sekarang via WhatsApp
            </a>
          </div>
        </div>
      </article>
      <!-- ===== END KALKULATOR ===== -->

      <!-- ===== AKTIVITAS ===== -->
      <article class="activity" id="activity">
        <div class="title">
          <h2 class="titleText"><span>A</span>ktivitas Kami</h2>
          <p>Ini hanya bagian kecil dari serangkaian kegiatan kami.</p>
        </div>
        <div class="content">
          <div class="box">
            <div class="imgBx"><img src="Assets/actv1.jpg" alt="" /></div>
            <div class="text"><h3>Pusaka Keliling</h3></div>
          </div>
          <div class="box">
            <div class="imgBx"><img src="Assets/igPusaka.png" alt="" /></div>
            <div class="text">
              <h3><a href="https://www.instagram.com/pusakahimatif/" class="link" target="_blank" rel="noopener noreferrer">Instagram</a></h3>
            </div>
          </div>
          <div class="box">
            <div class="imgBx"><img src="Assets/actv3.jpg" alt="" /></div>
            <div class="text"><h3>Pemberdayaan</h3></div>
          </div>
          <div class="box">
            <div class="imgBx"><img src="Assets/actv4.jpg" alt="" /></div>
            <div class="text"><h3>Merchandise</h3></div>
          </div>
          <div class="box">
            <div class="imgBx"><img src="Assets/actv5.jpg" alt="" /></div>
            <div class="text"><h3>Seminar Kewirausahaan</h3></div>
          </div>
        </div>
        <div class="title">
          <a href="#" class="btn">Lihat Semuanya</a>
        </div>
      </article>

      <!-- ===== ARTIKEL (DINAMIS) ===== -->
      <article class="artikel-section" id="artikel">
        <div class="title">
          <h2 class="titleText"><span>A</span>rtikel Berita</h2>
          <p>Artikel terkini tentang Pusaka Himatif</p>
        </div>

        <?php if (empty($artikels)): ?>
          <div class="artikel-empty">
            <p>Belum ada artikel yang dipublikasikan.</p>
          </div>
        <?php else: ?>
          <div class="artikel-grid">
            <?php foreach ($artikels as $art): ?>
              <div class="artikel-card">
                <a href="artikel.php?id=<?= $art['id'] ?>" class="artikel-card-link">
                  <div class="artikel-img-wrap">
                    <?php if ($art['gambar']): ?>
                      <img
                        src="uploads/artikel/<?= htmlspecialchars($art['gambar']) ?>"
                        alt="<?= htmlspecialchars($art['judul']) ?>"
                        class="artikel-img"
                        loading="lazy"
                      />
                    <?php else: ?>
                      <div class="artikel-img-placeholder">📰</div>
                    <?php endif; ?>
                  </div>
                  <div class="artikel-card-body">
                    <span class="artikel-date"><?= formatTanggal($art['created_at']) ?></span>
                    <h3 class="artikel-title"><?= htmlspecialchars($art['judul']) ?></h3>
                    <?php if ($art['ringkasan']): ?>
                      <p class="artikel-ringkasan"><?= htmlspecialchars(mb_strimwidth($art['ringkasan'], 0, 110, '…')) ?></p>
                    <?php endif; ?>
                    <span class="artikel-read-more">Baca Selengkapnya →</span>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </article>

      <!-- ===== TENTANG ===== -->
      <article class="about" id="about">
        <div class="row">
          <div class="col50">
            <h2 class="titleText"><span>T</span>entang Kami</h2>
            <p>
              Divisi Kewirausahaan memiliki beberapa program kerja salah satunya
              yaitu PUSAKA HIMATIF, disini kita mempunyai bidang usaha yaitu
              dalam bidang percetakan. Pusat alat tulis kantor himatif adalah
              koperasi yang menyediakan perlengkapan tulis mahasiswa maupun jasa
              cetak yang menunjang kebutuhan mahasiswa yang bertujuan untuk
              memudahkan mahasiswa dalam mencari jasa percetakan di area kampus
            </p>
          </div>
          <div class="col50">
            <div class="imgBx">
              <img src="Assets/img1.jpg" alt="" />
            </div>
          </div>
        </div>
      </article>

      <!-- ===== ULASAN ===== -->
      <aside class="testimonials" id="testimonials">
        <div class="title white">
          <h2 class="titleText"><span>U</span>lasan Tentang Kami</h2>
          <p>Testimoni dari beberapa orang.</p>
        </div>
        <div class="content">
          <div class="box">
            <div class="imgBx"><img src="Assets/account.png" alt="" /></div>
            <div class="text">
              <p>Pelaayanan ramah, pengerjaan cepat dan yang pasti harga terjangkau.</p>
              <h3>Farid Firmansyah</h3>
            </div>
          </div>
          <div class="box">
            <div class="imgBx"><img src="Assets/account.png" alt="" /></div>
            <div class="text">
              <p>Tempat sangat strategis bagi mahasiswa yang membutuhkan jasa percetakan terkhusus di area kampus.</p>
              <h3>Putra SH</h3>
            </div>
          </div>
          <div class="box">
            <div class="imgBx"><img src="Assets/account.png" alt="" /></div>
            <div class="text">
              <p>Staff sangat ramah, adanya Pusaka Keliling memudahkan mobilitas mahasiswa disaat urgent</p>
              <h3>Ade Kurnia Putri</h3>
            </div>
          </div>
        </div>
      </aside>

      <!-- ===== KONTAK ===== -->
      <article class="contact" id="contact">
        <div class="title">
          <h2 class="titleText"><span>T</span>ulis Ulasanmu</h2>
          <p>
            Jika layanan dari pusaka buruk beritahu kami, jika layanan dari kami
            baik beritahu teman-temanmu.
          </p>
        </div>
        <form
          action="https://formspree.io/f/xdovwdor"
          method="POST"
          class="contactForm"
          id="contact-form"
        >
          <h3>Kirim Ulasan</h3>
          <div class="inputBox">
            <input type="text" placeholder="Nama" name="nama" />
          </div>
          <div class="inputBox">
            <input type="text" placeholder="Email" name="email" />
          </div>
          <div class="inputBox">
            <input type="text" placeholder="Subjek" name="subjek" />
          </div>
          <div class="inputBox">
            <textarea type="text" placeholder="message" name="textarea"></textarea>
          </div>
          <div class="inputBox">
            <input type="submit" value="Kirim" name="submit" />
          </div>
          <div class="lds-ellipsis">
            <div></div><div></div><div></div><div></div>
          </div>
        </form>
      </article>
    </main>

    <footer>
      <div class="copyrightText">
        <p>
          copyright 2023
          <a
            href="https://instagram.com/pusaka.himatif?igshid=NTA5ZTk1NTc="
            target="_blank"
            rel="nofollow"
            title="Menuju Instagram"
          >Divisi Kewirausahaan</a>. All Right Reserved
          &nbsp;|&nbsp;
          <a href="admin/login.php" style="color:#9fdaed;opacity:0.6;font-size:0.85em;">Admin</a>
        </p>
      </div>
    </footer>

    <script type="text/javascript" src="script.js"></script>
  </body>
</html>
