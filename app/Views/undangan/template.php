<?php
// filepath: app/Views/undangan/template.php
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Surat Undangan - <?= esc($kegiatan['nama_kegiatan']) ?></title>
  <style>
    @page {
      margin: 2cm;
      size: A4;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 12pt;
      line-height: 1.4;
      color: #000;
    }

    .container {
      width: 100%;
      max-width: 21cm;
      margin: 0 auto;
      padding: 0;
    }

    /* Header dengan Logo */
    .header {
      display: table;
      width: 100%;
      margin-bottom: 30px;
      border-bottom: 3px solid #000;
      padding-bottom: 15px;
    }

    .logo-left,
    .logo-right {
      display: table-cell;
      width: 80px;
      vertical-align: middle;
      text-align: center;
    }

    .logo-img {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      border: 2px solid #000;
    }

    .header-content {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
      padding: 0 20px;
    }

    .org-name {
      font-size: 18pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 5px;
    }

    .address {
      font-size: 10pt;
      margin-bottom: 2px;
    }

    /* Nomor dan Tanggal */
    .doc-info {
      margin: 25px 0;
    }

    .doc-row {
      display: table;
      width: 100%;
      margin-bottom: 8px;
    }

    .doc-label {
      display: table-cell;
      width: 100px;
      font-weight: bold;
    }

    .doc-value {
      display: table-cell;
      padding-left: 10px;
    }

    /* Konten Surat */
    .content {
      margin: 30px 0;
      text-align: justify;
      line-height: 1.6;
    }

    .greeting {
      margin-bottom: 20px;
    }

    .event-details {
      margin: 25px 0;
      padding: 20px;
      background-color: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    .detail-row {
      display: table;
      width: 100%;
      margin-bottom: 10px;
    }

    .detail-label {
      display: table-cell;
      width: 120px;
      font-weight: bold;
      padding-right: 10px;
    }

    .detail-colon {
      display: table-cell;
      width: 20px;
    }

    .detail-value {
      display: table-cell;
    }

    /* Penutup */
    .closing {
      margin-top: 40px;
      text-align: justify;
    }

    /* Signature */
    .signature-section {
      margin-top: 50px;
      display: table;
      width: 100%;
    }

    .signature-left,
    .signature-right {
      display: table-cell;
      width: 50%;
      text-align: center;
      vertical-align: top;
    }

    .signature-title {
      font-weight: bold;
      margin-bottom: 60px;
    }

    .signature-name {
      font-weight: bold;
      border-bottom: 1px solid #000;
      display: inline-block;
      padding-bottom: 2px;
      min-width: 200px;
    }

    .signature-position {
      font-size: 10pt;
      margin-top: 5px;
    }

    /* Footer Note */
    .footer-note {
      margin-top: 40px;
      font-size: 10pt;
      font-style: italic;
      text-align: left;
    }

    /* Print Styles */
    @media print {
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .container {
        width: 100%;
        margin: 0;
        padding: 0;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header dengan Logo -->
    <div class="header">
      <div class="logo-left">
        <div
          style="width: 70px; height: 70px; background: #dc3545; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24pt;">
          PB
        </div>
      </div>
      <div class="header-content">
        <div class="org-name">PAGUYUBAN PEMUDA BITUNG</div>
        <div class="address">Sekretariat : Jl. Bitung Giresuk No. 73 Ds. Kadujaya Kec. Curug Tangerang</div>
      </div>
      <div class="logo-right">
        <div
          style="width: 70px; height: 70px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16pt;">
          75<br><small style="font-size: 8pt;">Thn</small>
        </div>
      </div>
    </div>

    <!-- Nomor dan Tanggal Surat -->
    <div class="doc-info">
      <div class="doc-row">
        <div class="doc-label">Nomor</div>
        <div class="doc-value">: <?= esc($nomor_undangan) ?></div>
      </div>
      <div class="doc-row">
        <div class="doc-label">Lamp</div>
        <div class="doc-value">: -</div>
      </div>
      <div class="doc-row">
        <div class="doc-label">Perihal</div>
        <div class="doc-value">: <strong>Undangan Kongan Sholawat</strong></div>
      </div>
      <div style="text-align: right; margin-top: 20px;">
        <?= strftime('%A, %d %B %Y', strtotime($tanggal_undangan)) ?>
      </div>
      <div style="text-align: right; margin-top: 5px;">
        Kepada Yth :<br>
        Bapak/Sdr : _______________<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;di<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Tempat</strong>
      </div>
    </div>

    <!-- Konten Surat -->
    <div class="content">
      <div class="greeting">
        <strong>Innallahi wa innallahi rojiun,</strong><br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dengan adanya berita duka telah meninggal dunia Saudara/Anggota
        kita <strong>Alm. <?= esc($kegiatan['nama_anggota']) ?></strong>, kami mengajak rekan-rekan agar dapat hadir
        kongan sholawat sekaligus mendo'akan beliau pada :
      </div>

      <div class="event-details">
        <div style="font-weight: bold; margin-bottom: 15px; text-align: center; font-size: 14pt;">
          <?= esc($kegiatan['nama_kegiatan']) ?>:
        </div>

        <div class="detail-row">
          <div class="detail-label">Hari / Tanggal</div>
          <div class="detail-colon">:</div>
          <div class="detail-value">
            <strong><?= strftime('%A, %d %B %Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></strong>
          </div>
        </div>

        <div class="detail-row">
          <div class="detail-label">Acara</div>
          <div class="detail-colon">:</div>
          <div class="detail-value"><strong><?= esc($kegiatan['nama_kegiatan']) ?></strong></div>
        </div>

        <div class="detail-row">
          <div class="detail-label">Waktu</div>
          <div class="detail-colon">:</div>
          <div class="detail-value"><strong>19.00 s/d 20.30 WIB</strong></div>
        </div>

        <div class="detail-row">
          <div class="detail-label">Tempat</div>
          <div class="detail-colon">:</div>
          <div class="detail-value"><strong><?= esc($tempat_kegiatan) ?></strong></div>
        </div>
      </div>

      <div class="closing">
        Demikian undangan ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih.
      </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
      <div class="signature-left">
        <div class="signature-title">Ketua,</div>
        <div class="signature-name">Ridwan Iskandar, S.M</div>
      </div>
      <div class="signature-right">
        <div class="signature-title">Sekretaris</div>
        <div class="signature-name">Muhamad Hasan Bakrie, S.Kom</div>
      </div>
    </div>

    <!-- Footer Note -->
    <div class="footer-note">
      <strong>Note :</strong> Anggota wajib mengikuti kongan Min. 10.000,-
    </div>
  </div>
</body>

</html>