<?php

class Nilai {
    private $tugas;
    private $ujian;

    public function __construct($tugas, $ujian) {
        $this->tugas = $tugas;
        $this->ujian = $ujian;
    }

    public static function hitungAkhir($tugas, $ujian) {
        return ($tugas * 0.4) + ($ujian * 0.6);
    }

    public function getTugas() {
        return $this->tugas;
    }

    public function getUjian() {
        return $this->ujian;
    }
}