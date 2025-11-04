<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Hasil Kongan - <?= esc($kegiatan['nama_kegiatan']) ?></title>
  <style>
  body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    margin: 20px;
    color: #333;
  }

  .header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 15px;
  }

  .header h1 {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
  }

  .header h2 {
    margin: 5px 0 0 0;
    font-size: 14px;
    color: #7f8c8d;
    font-weight: normal;
  }

  .info-section {
    margin-bottom: 25px;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
  }

  .info-row {
    margin-bottom: 8px;
    display: flex;
  }

  .info-label {
    width: 120px;
    font-weight: bold;
    color: #2c3e50;
  }

  .info-value {
    flex: 1;
    color: #34495e;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 11px;
  }

  .table-header {
    background-color: #3498db;
    color: white;
  }

  .table-header th {
    padding: 12px 8px;
    text-align: left;
    font-weight: bold;
  }

  .table-body td {
    padding: 10px 8px;
    border-bottom: 1px solid #ecf0f1;
  }

  .table-body tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .text-center {
    text-align: center;
  }

  .text-right {
    text-align: right;
  }

  .text-bold {
    font-weight: bold;
  }

  .summary-section {
    margin-top: 25px;
    background-color: #ecf0f1;
    padding: 15px;
    border-radius: 5px;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
  }

  .summary-label {
    font-weight: bold;
    color: #2c3e50;
  }

  .summary-value {
    color: #27ae60;
    font-weight: bold;
  }

  .summary-negative {
    color: #e74c3c;
  }

  .total-final {
    border-top: 2px solid #2c3e50;
    margin-top: 10px;
    padding-top: 10px;
    font-size: 14px;
  }

  .footer {
    margin-top: 40px;
    text-align: center;
    font-size: 10px;
    color: #7f8c8d;
    border-top: 1px solid #ecf0f1;
    padding-top: 15px;
  }

  .signature-section {
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
  }

  .signature-box {
    width: 200px;
    text-align: center;
  }

  .signature-line {
    border-bottom: 1px solid #333;
    margin-top: 50px;
    margin-bottom: 5px;
  }
  </style>
</head>

<body>
  <div class="header">
    <h1>HASIL KONGAN KEGIATAN</h1>
    <h2><?= esc($kegiatan['nama_kegiatan']) ?></h2>
  </div>

  <div class="info-section">
    <div class="info-row">
      <span class="info-label">Nama Kegiatan:</span>
      <span class="info-value"><?= esc($kegiatan['nama_kegiatan']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Penyelenggara:</span>
      <span class="info-value"><?= esc($kegiatan['nama_anggota']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Tanggal Kegiatan:</span>
      <span class="info-value"><?= date('d F Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Total Peserta:</span>
      <span class="info-value"><?= count($kongan) ?> orang</span>
    </div>
    <div class="info-row">
      <span class="info-label">Tanggal Cetak:</span>
      <span class="info-value"><?= date('d F Y H:i') ?> WIB</span>
    </div>
  </div>

  <table>
    <thead class="table-header">
      <tr>
        <th width="8%" class="text-center">No</th>
        <th width="50%">Nama Anggota</th>
        <th width="25%" class="text-right">Jumlah Kongan</th>
      </tr>
    </thead>
    <tbody class="table-body">
      <?php if (!empty($kongan)): ?>
      <?php $no = 1; ?>
      <?php foreach ($kongan as $row): ?>
      <tr>
        <td class="text-center"><?= $no++ ?></td>
        <td><?= esc($row['nama_anggota']) ?></td>
        <td class="text-right text-bold">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="4" class="text-center" style="padding: 30px; color: #7f8c8d;">
          Tidak ada data kongan
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if (!empty($kongan)): ?>
  <div class="summary-section">
    <div class="summary-row">
      <span class="summary-label">Total Kongan:</span>
      <span class="summary-value">Rp <?= number_format($total_kongan, 0, ',', '.') ?></span>
    </div>

    <div class="summary-row">
      <span class="summary-label">Potongan 10% Total Kongan:</span>
      <span class="summary-value summary-negative">- Rp <?= number_format($sepuluh_persen, 0, ',', '.') ?></span>
    </div>

    <?php if (!$anggota_aktif_di_kegiatan_lain): ?>
    <div class="summary-row">
      <span class="summary-label">Potongan Tidak Nulis Kegiatan Lain (0x ikut):</span>
      <span class="summary-value summary-negative">- Rp <?= number_format($potonganTidakIkut, 0, ',', '.') ?></span>
    </div>
    <?php else: ?>
    <div class="summary-row">
      <span class="summary-label">Anggota sangat aktif:</span>
    </div>
    <?php endif; ?>

    <div class="summary-row">
      <span class="summary-label">Potongan Undangan:</span>
      <span class="summary-value summary-negative">- Rp <?= number_format($potongan_undangan, 0, ',', '.') ?></span>
    </div>

    <div class="summary-row total-final">
      <span class="summary-label" style="font-size: 14px;">TOTAL BERSIH:</span>
      <span class="summary-value" style="font-size: 16px; color: #27ae60;">Rp
        <?= number_format($total_bersih, 0, ',', '.') ?></span>
    </div>
  </div>
  <?php endif; ?>

  <div class="signature-section">
    <div class="signature-box">
      <p><strong>Mengetahui,</strong></p>
      <p>Ketua Pemuda Bitung</p>
      <div class="signature-line"></div>
      <p>(Ridwan Iskandar, S.M)</p>
    </div>
  </div>

  <div class="footer">
    <p>Dokumen ini dibuat secara otomatis oleh Sistem Kongan PB</p>
    <p>Dicetak pada: <?= date('d F Y \p\u\k\u\l H:i') ?> WIB</p>
  </div>
</body>

</html>