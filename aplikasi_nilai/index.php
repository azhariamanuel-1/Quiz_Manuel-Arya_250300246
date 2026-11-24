
<?php
require_once "koneksi.php";
require_once "Mahasiswa.php";
require_once "Nilai.php";

$pesan = "";

if (isset($_POST['simpan'])) {

    $mahasiswa = new Mahasiswa(
        $_POST['nama'],
        $_POST['nim']
    );

    $nilai = new Nilai(
        $_POST['tugas'],
        $_POST['ujian']
    );

    $akhir = Nilai::hitungAkhir(
        $nilai->getTugas(),
        $nilai->getUjian()
    );

    $nim = $mahasiswa->getNim();
    $nama = $mahasiswa->getNama();

    $stmt = $koneksi->prepare(
        "INSERT INTO mahasiswa (nim, nama) VALUES (?, ?)"
    );

    $stmt->bind_param("ss", $nim, $nama);

    if ($stmt->execute()) {

        $mahasiswa_id = $koneksi->insert_id;

        $tugas = $nilai->getTugas();
        $ujian = $nilai->getUjian();

        $stmt2 = $koneksi->prepare(
            "INSERT INTO nilai
            (mahasiswa_id, nilai_tugas, nilai_ujian, nilai_akhir)
            VALUES (?, ?, ?, ?)"
        );

        $stmt2->bind_param(
            "iddd",
            $mahasiswa_id,
            $tugas,
            $ujian,
            $akhir
        );

        if ($stmt2->execute()) {
            $pesan = "Data berhasil disimpan!";
        } else {
            $pesan = "Gagal menyimpan nilai.";
        }

    } else {
        $pesan = "Gagal menyimpan mahasiswa.";
    }
}

$data = $koneksi->query("
    SELECT mahasiswa.nama, mahasiswa.nim,
           nilai.nilai_tugas, nilai.nilai_ujian,
           nilai.nilai_akhir
    FROM mahasiswa
    INNER JOIN nilai
    ON mahasiswa.id = nilai.mahasiswa_id
    ORDER BY nilai.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aplikasi Nilai Mahasiswa</title>
</head>

<body>

<h2>Aplikasi Penghitung Nilai Mahasiswa</h2>

<p><?php echo $pesan; ?></p>

<form method="POST">

    <p>Nama Mahasiswa</p>
    <input type="text" name="nama" required>

    <p>NIM</p>
    <input type="text" name="nim" required>

    <p>Nilai Tugas</p>
    <input type="number" name="tugas"
    min="0" max="100" step="any" required>

    <p>Nilai Ujian</p>
    <input type="number" name="ujian"
    min="0" max="100" step="any" required>

    <br><br>

    <button type="submit" name="simpan">
        Hitung dan Simpan
    </button>

</form>

<hr>

<h3>Daftar Nilai Mahasiswa</h3>

<table border="1" cellpadding="8">

<tr>
    <th>NIM</th>
    <th>Nama</th>
    <th>Nilai Tugas</th>
    <th>Nilai Ujian</th>
    <th>Nilai Akhir</th>
</tr>

<?php while ($row = $data->fetch_assoc()) { ?>

<tr>
    <td><?= htmlspecialchars($row['nim']) ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td><?= $row['nilai_tugas'] ?></td>
    <td><?= $row['nilai_ujian'] ?></td>
    <td><?= number_format($row['nilai_akhir'], 2) ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>
