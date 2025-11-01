<?php include 'header.php'; ?>

    <section id="home" class="hero fade-up">
      <div class="slides"> <div class="slide active" style="background-image: url('images/hero1.jpg')"></div> <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e');"></div> <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee');"></div> </div>
      <div class="hero-overlay"></div>
      <div class="hero-content"> <h1>Back to Nature🍃</h1> <p>Sewa alat camping gunung & outdoor Kalimantan.</p> <a href="#katalog" class="btn-hero">Lihat Katalog</a> </div>
    </section>

    <section id="contact" class="official fade-up">
      <h2>Official Account</h2>
      <div class="social-container"> <a href="https://instagram.com/boemicamp_adventure" target="_blank" class="social-card"> <img src="images/instagram.png" alt="Instagram" /> <span>@boemicamp_adventure</span> </a> <a href="https://www.tiktok.com/@boemicamp_adventure?_t=ZS-90sDh09Cvua&_r=1" target="_blank" class="social-card"> <img src="images/tiktok.png" alt="TikTok" /> <span>@boemicamp_adventure</span> </a> <a href="https://wa.me/62895354293657" target="_blank" class="social-card"> <img src="images/whatsapp.png" alt="WhatsApp" /> <span>+62 895-3542-93657</span> </a> </div>
    </section>

    <section id="katalog" class="katalog fade-up">
      <h2>Katalog Alat Camping</h2>
      <div class="katalog-slider-wrapper">
        <button class="slider-btn prev"><</button>
        <div class="katalog-container">
          
          <?php
            include 'koneksi.php'; 
            
            // Kueri 1: Ambil produk yang BUKAN bagian dari grup
            $query_katalog = "SELECT * FROM products WHERE group_id IS NULL ORDER BY name ASC";
            $result_katalog = mysqli_query($koneksi, $query_katalog);
            
            while ($data = mysqli_fetch_assoc($result_katalog)):
          ?>
          
          <div class="card">
            <img src="images/<?php echo htmlspecialchars($data['image_url']); ?>" alt="<?php echo htmlspecialchars($data['name']); ?>" />
            <h3><?php echo htmlspecialchars($data['name']); ?></h3>
            <p>Rp <?php echo number_format($data['price']); ?> / hari</p>

            <?php if ($data['stock'] > 0): ?>
                <form action="keranjang_aksi.php" method="POST" style="margin-bottom: 0;">
                    <input type="hidden" name="product_id" value="<?php echo $data['id']; ?>">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="btn-small">Tambah ke Keranjang</button>
                </form>
            <?php else: ?>
                <button class="btn-small" disabled style="background-color: #aaa; cursor: not-allowed;">Stok Habis</button>
            <?php endif; ?>
          </div>

          <?php 
            endwhile;
            // JANGAN tutup koneksi di sini
          ?>

        </div>
        <button class="slider-btn next">></button>
      </div>
    </section>

    <section id="paket" class="paket fade-up">
      <h2>Paket Hemat</h2>
      <div class="paket-container">
        
        <?php
          // Kueri 2: Ambil 3 grup paket (Bucin, Bestie, Circle)
          $query_grup = "SELECT * FROM paket_grup ORDER BY group_id ASC";
          $result_grup = mysqli_query($koneksi, $query_grup);
          
          // Loop 1: Untuk setiap grup...
          while ($grup = mysqli_fetch_assoc($result_grup)):
            $current_group_id = $grup['group_id'];
        ?>
        
        <div class="card paket">
          <img src="images/<?php echo htmlspecialchars($grup['group_image_url']); ?>" alt="<?php echo htmlspecialchars($grup['group_name']); ?>" />
          <h3><?php echo htmlspecialchars($grup['group_name']); ?></h3>
          
          <form action="keranjang_aksi.php" method="POST" style="margin-bottom: 0;">
            <input type="hidden" name="action" value="add">
            
            <ul class="price-list">
              <?php
                // Kueri 3: Ambil SEMUA produk/opsi yang termasuk dalam grup ini
                $query_opsi = "SELECT * FROM products WHERE group_id = $current_group_id ORDER BY price ASC";
                $result_opsi = mysqli_query($koneksi, $query_opsi);
                $first_option = true; // Untuk menandai radio button pertama
                $harga_mulai_dari = 0;

                // Loop 2: Untuk setiap opsi di dalam grup...
                while ($opsi = mysqli_fetch_assoc($result_opsi)):
                  if ($first_option) {
                      $harga_mulai_dari = $opsi['price']; // Ambil harga opsi termurah
                  }
              ?>
                <li>
                  <input type="radio" 
                         name="product_id" 
                         value="<?php echo $opsi['id']; ?>" 
                         id="opt_<?php echo $opsi['id']; ?>"
                         <?php if ($first_option) { echo 'checked'; $first_option = false; } // Pilih yg pertama otomatis ?>
                         <?php if ($opsi['stock'] <= 0) { echo 'disabled'; } // Nonaktifkan jika stok habis ?>
                  >
                  
                  <label for="opt_<?php echo $opsi['id']; ?>" style="width: 100%; display: flex; justify-content: space-between; cursor: pointer;">
                    <span><?php echo htmlspecialchars($opsi['name']); ?> <?php if ($opsi['stock'] <= 0) { echo '(Habis)'; } ?></span>
                    <span style="color: #00cc7a; font-weight: 600;">Rp <?php echo number_format($opsi['price']); ?></span>
                  </label>
                </li>
              <?php endwhile; ?>
            </ul>
            
            <p><strong>Mulai dari Rp <?php echo number_format($harga_mulai_dari); ?></strong></p>
            
            <button type="submit" class="btn-small">Sewa Paket</button>
          </form>
        </div>
        
        <?php 
          endwhile;
          mysqli_close($koneksi); // Tutup koneksi
        ?>
        
      </div>
    </section>

    <section id="testimoni" class="testimoni fade-up">
      <script src="https://elfsightcdn.com/platform.js" async></script>
      <div class="elfsight-app-3e89a831-efc2-49df-8629-33cedfd78a47" data-elfsight-app-lazy></div>
    </section>

    <section id="about" class="about fade-up">
      <h2>Tentang Kami</h2>
      <div class="about-content">
        <img src="images/boemi.jpg" alt="Boemi Camp Adventure" />
        <div class="about-text">
          <h3>Boemi Camp Adventure</h3>
          <p>Boemi Camp Adventure adalah penyedia layanan sewa alat camping terbaik di Kalimantan Barat...</p>
          <p>Dengan pelayanan cepat, harga terjangkau, dan tim yang ramah...</p>
          <a href="https://wa.me/62895354293657?text=..." target="_blank" class="btn-about">Hubungi Kami di WhatsApp</a>
        </div>
      </div>
    </section>

<?php include 'footer.php'; ?>