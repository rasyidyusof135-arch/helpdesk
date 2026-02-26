<?php
// ⚠️ DELETE THIS FILE AFTER RUNNING
require 'db_connect.php';

// Secret key to prevent accidental runs
if (!isset($_GET['key']) || $_GET['key'] !== 'seed2026') {
    die("❌ Access denied. Add ?key=seed2026 to the URL.");
}

// Check if already seeded
$check = $conn->query("SELECT COUNT(*) as total FROM tickets WHERE reporter_email LIKE '%@company.com'");
$row = $check->fetch_assoc();
if ($row['total'] > 0) {
    die("⚠️ Data sudah ada ({$row['total']} tickets @company.com). Seed tidak dijalankan semula.");
}

// asset_id: 1=PC001(Marketing), 2=PCHR01(HR), 3=PCFIN01(Finance), 4=PCSALES01(Sales), 5=PCOPS01(Operations)
// status enum: Open | In Progress | Level 2 (Technical Support) | Level 3 (Infrastructure/System Admin) | Under Warranty Claim | Pending Parts | Closed

$tickets = [
    // --- PC-Marketing-01 (asset_id=1) ---
    [1,'Ahmad Farid','0123456789','ahmad.farid@company.com','Monitor tidak berfungsi, skrin hitam sepenuhnya.','Open',''],
    [1,'Nurul Ain','0198765432','nurul.ain@company.com','Komputer tidak boleh boot, terkandas di logo Windows.','In Progress','Admin sedang semak perkakasan.'],
    [1,'Razif Azman','0112233445','razif@company.com','Keyboard beberapa butang tidak berfungsi.','Closed','Keyboard telah ditukar dengan yang baru.'],
    [1,'Siti Hajar','0167788990','siti.hajar@company.com','Komputer berjalan sangat perlahan semasa buka aplication.','In Progress','Sedang buat disk cleanup dan scan virus.'],
    [1,'Haikal Nizam','0134455667','haikal@company.com','USB port tidak detect pemacu kilat.','Open',''],
    [1,'Farahiyah','0189900112','farahiyah@company.com','Microsoft Office tiba-tiba keluar error dan tutup sendiri.','Closed','Office telah diinstall semula.'],
    [1,'Zulaikha Rina','0145566778','zulaikha@company.com','Komputer restart sendiri setiap 30 minit.','Level 2 (Technical Support)','Dihantar ke Level 2 untuk diagnosis lanjut.'],
    [1,'Luqman Hakim','0176677889','luqman@company.com','Printer tidak dapat dikesan oleh komputer ini.','In Progress','Sedang semak driver printer.'],
    [1,'Aisyah Balqis','0121122334','aisyah@company.com','Bunyi bising dari dalam casing PC semasa digunakan.','Pending Parts','Menunggu kipas gantian.'],
    [1,'Ridhwan Azmi','0155566779','ridhwan@company.com','Internet sambungan sangat lembab di komputer ini sahaja.','Closed','Network adapter driver telah dikemaskini.'],

    // --- PC-HR-01 (asset_id=2) ---
    [2,'Sharifah Nora','0113344556','sharifah@company.com','Sistem HRIS tidak dapat diakses, ralat log masuk.','Open',''],
    [2,'Mohd Fadzli','0188899001','fadzli@company.com','Fail Excel rosak dan tidak dapat dibuka.','Closed','Fail telah dipulihkan dari backup.'],
    [2,'Nabilah Sofea','0144455668','nabilah@company.com','Skrin monitor berkelip-kelip.','In Progress','Kabel VGA sedang ditukar.'],
    [2,'Zainudin Bakar','0177788901','zainudin@company.com','Komputer tidak dapat capai share drive HR.','Open',''],
    [2,'Haziqah Musa','0122233445','haziqah@company.com','Antivirus telah luput, perlukan renewal.','In Progress','Sedang proses pembelian lesen baru.'],
    [2,'Amirul Hafiz','0156677890','amirul@company.com','Speaker tidak mengeluarkan bunyi.','Closed','Driver audio telah diinstall semula.'],
    [2,'Roshaidah','0189900223','roshaidah@company.com','Windows update gagal, error code 0x800f0922.','Level 3 (Infrastructure/System Admin)','Dihantar ke sysadmin untuk semak WSUS.'],
    [2,'Khairul Ikhwan','0112233556','khairul@company.com','Mouse rosak, cursor tidak bergerak.','Closed','Mouse telah ditukar dengan yang baru.'],
    [2,'Faizatul Akma','0145566889','faizatul@company.com','Zoom tidak boleh detect kamera semasa meeting.','In Progress','Sedang kemaskini driver webcam.'],
    [2,'Norashikin','0178899012','norashikin@company.com','Blue screen of death (BSOD) muncul tiba-tiba.','Level 2 (Technical Support)','Sedang analisa dump file.'],

    // --- PC-Finance-01 (asset_id=3) ---
    [3,'Kamarulzaman','0113344667','kamarulzaman@company.com','Sistem perakaunan tidak dapat beroperasi.','In Progress','IT sedang hubungi vendor sistem.'],
    [3,'Suriati Mhd','0188899112','suriati@company.com','Komputer terlalu panas, fan bunyi kuat.','Pending Parts','Menunggu thermal paste dan fan baru.'],
    [3,'Hairul Nizam','0144455779','hairul@company.com','Fail PDF tidak dapat dibuka selepas Windows update.','Closed','Adobe Reader dikemaskini ke versi terbaru.'],
    [3,'Norliza Jamil','0177789012','norliza@company.com','Sambungan VPN ke server bermasalah.','Open',''],
    [3,'Syazwan Zaki','0122234556','syazwan@company.com','Hard disk hampir penuh, hanya baki 2GB.','In Progress','Sedang pindah data ke external storage.'],
    [3,'Mazizah Said','0156678001','mazizah@company.com','Email Outlook tidak boleh send/receive.','Closed','Konfigurasi SMTP telah dibetulkan.'],
    [3,'Hafizudin','0189900334','hafizudin@company.com','Komputer tidak dapat cetak ke printer Finance.','Open',''],
    [3,'Suraya Halim','0112234667','suraya@company.com','TeamViewer tidak boleh disambungkan untuk remote support.','In Progress','Sedang semak firewall settings.'],
    [3,'Ruslan Ariff','0145567890','ruslan@company.com','Data Excel hilang selepas komputer restart mengejut.','Closed','Data dipulihkan dari auto-recovery file.'],
    [3,'Hartini Osman','0178890123','hartini@company.com','Skrin tiba-tiba jadi hitam semasa kerja, perlu restart.','Under Warranty Claim','Unit dibawa ke servis warranty.'],

    // --- PC-Sales-01 (asset_id=4) ---
    [4,'Alif Hazwan','0113345678','alif@company.com','CRM sistem sangat lembab semasa buka rekod pelanggan.','In Progress','Sedang optimise RAM.'],
    [4,'Diyana Yusof','0188900223','diyana@company.com','Wi-Fi terputus berulang kali di workstation ini.','Open',''],
    [4,'Farhana Idris','0144456780','farhana@company.com','Bahasa papan kekunci bertukar sendiri kepada bahasa lain.','Closed','Language settings telah ditetapkan semula.'],
    [4,'Zulkefli Aman','0177890123','zulkefli@company.com','Skype for Business tidak boleh log in.','Open',''],
    [4,'Nor Liyana','0122345667','norliyana@company.com','Power supply masalah, PC tiba-tiba mati bila guna.','Pending Parts','Menunggu PSU gantian.'],
    [4,'Hafeez Rahman','0156779012','hafeez@company.com','Windows Explorer crash berulang kali.','Closed','System file checker telah dijalankan dan diperbaiki.'],
    [4,'Ruzaini Latif','0189901345','ruzaini@company.com','PC tidak dapat detect WiFi adapter.','Open',''],
    [4,'Airina Saleem','0112345778','airina@company.com','Pop-up iklan muncul berterusan, mungkin ada virus.','Closed','Malware telah dibuang, sistem dibersihkan.'],
    [4,'Fadhli Arman','0145678901','fadhli@company.com','Google Chrome tidak boleh dibuka, icon hilang.','In Progress','Sedang install semula browser.'],
    [4,'Nazurah Ishak','0178901234','nazurah@company.com','Webcam tidak berfungsi semasa video call dengan pelanggan.','Level 2 (Technical Support)','Dihantar Level 2 untuk semak hardware.'],

    // --- PC-Operations-01 (asset_id=5) ---
    [5,'Shafiq Hanif','0113456789','shafiq@company.com','Sistem inventory tidak dapat diakses selepas password reset.','In Progress','IT sedang reset semula akaun.'],
    [5,'Norzaimah','0189012456','norzaimah@company.com','Fail penting dalam recycle bin dan tidak dapat dikembalikan.','Closed','Fail dipulihkan dengan software recovery.'],
    [5,'Amirullail','0144567891','amirullail@company.com','PC sering hang terutama semasa buka multiple applications.','Open',''],
    [5,'Khadijah Nor','0177901234','khadijah@company.com','Tempoh lesen Microsoft 365 hampir tamat.','In Progress','Proses renewal sedang berjalan.'],
    [5,'Roslan Daud','0122456778','roslan@company.com','Printer tidak mencetak walaupun queue document ada.','Closed','Print spooler service telah di-restart.'],
    [5,'Mardiana Jusoh','0156890123','mardiana@company.com','Fail backup tidak dapat disimpan ke network drive.','Open',''],
    [5,'Hidayah Ramli','0189012567','hidayah@company.com','Sistem email terlalu lembab untuk buka attachment besar.','In Progress','Sedang semak mailbox quota.'],
    [5,'Norfadzilah','0112456889','norfadzilah@company.com','Tetikus wireless tidak berfungsi, bateri dah tukar tapi sama.','Closed','Receiver USB dongle telah ditukar.'],
    [5,'Jamil Kadir','0145789012','jamil@company.com','Komputer tidak dapat detect monitor kedua (dual monitor setup).','Under Warranty Claim','Unit dihantar untuk tuntutan warranty display.'],
    [5,'Suzana Md Ali','0179012345','suzana@company.com','Windows activation hilang selepas format.','In Progress','IT sedang dapatkan product key dari stok.'],
];

$inserted = 0;
$errors = 0;

foreach ($tickets as $t) {
    $asset_id      = (int)$t[0];
    $reporter_name = $conn->real_escape_string($t[1]);
    $reporter_phone= $conn->real_escape_string($t[2]);
    $reporter_email= $conn->real_escape_string($t[3]);
    $description   = $conn->real_escape_string($t[4]);
    $status        = $conn->real_escape_string($t[5]);
    $admin_remarks = $conn->real_escape_string($t[6]);

    $sql = "INSERT INTO tickets (asset_id, reporter_name, reporter_phone, reporter_email, description, status, admin_remarks)
            VALUES ($asset_id, '$reporter_name', '$reporter_phone', '$reporter_email', '$description', '$status', '$admin_remarks')";

    if ($conn->query($sql) === TRUE) {
        $inserted++;
        echo "✅ [{$t[0]}] {$t[1]}<br>";
    } else {
        echo "❌ Error [{$t[1]}]: " . $conn->error . "<br>";
        $errors++;
    }
}

echo "<hr><strong>✅ Selesai! Inserted: $inserted | Errors: $errors</strong><br>";
echo "<br>⚠️ <strong>SILA DELETE fail seed_railway.php dari server selepas ini!</strong>";
$conn->close();
?>
