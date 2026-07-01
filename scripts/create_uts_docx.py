from datetime import date
from pathlib import Path
from zipfile import ZipFile, ZIP_DEFLATED
import html


OUT = Path("Soal_Praktik_UTS_Pemrograman_Web_Lanjut.docx")


def esc(text):
    return html.escape(str(text), quote=False)


def p(text="", style=None, bold=False, align=None):
    ppr = ""
    if style:
        ppr += f'<w:pStyle w:val="{style}"/>'
    if align:
        ppr += f'<w:jc w:val="{align}"/>'
    rpr = "<w:b/>" if bold else ""
    return (
        "<w:p>"
        + (f"<w:pPr>{ppr}</w:pPr>" if ppr else "")
        + f"<w:r><w:rPr>{rpr}</w:rPr><w:t>{esc(text)}</w:t></w:r>"
        + "</w:p>"
    )


def bullet(text):
    return (
        '<w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr>'
        '<w:spacing w:after="80" w:line="280" w:lineRule="auto"/></w:pPr>'
        f"<w:r><w:t>{esc(text)}</w:t></w:r></w:p>"
    )


def numbered(text):
    return (
        '<w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr>'
        '<w:spacing w:after="80" w:line="280" w:lineRule="auto"/></w:pPr>'
        f"<w:r><w:t>{esc(text)}</w:t></w:r></w:p>"
    )


def page_break():
    return '<w:p><w:r><w:br w:type="page"/></w:r></w:p>'


def table(rows, widths, header=True):
    grid = "".join(f'<w:gridCol w:w="{w}"/>' for w in widths)
    xml = [
        '<w:tbl><w:tblPr><w:tblW w:w="9360" w:type="dxa"/>'
        '<w:tblInd w:w="120" w:type="dxa"/>'
        '<w:tblBorders><w:top w:val="single" w:sz="6" w:color="DADCE0"/>'
        '<w:left w:val="single" w:sz="6" w:color="DADCE0"/>'
        '<w:bottom w:val="single" w:sz="6" w:color="DADCE0"/>'
        '<w:right w:val="single" w:sz="6" w:color="DADCE0"/>'
        '<w:insideH w:val="single" w:sz="6" w:color="DADCE0"/>'
        '<w:insideV w:val="single" w:sz="6" w:color="DADCE0"/></w:tblBorders>'
        '<w:tblCellMar><w:top w:w="80" w:type="dxa"/><w:bottom w:w="80" w:type="dxa"/>'
        '<w:start w:w="120" w:type="dxa"/><w:end w:w="120" w:type="dxa"/></w:tblCellMar>'
        "</w:tblPr>",
        f"<w:tblGrid>{grid}</w:tblGrid>",
    ]
    for ridx, row in enumerate(rows):
        xml.append("<w:tr>")
        for cidx, cell in enumerate(row):
            fill = '<w:shd w:fill="F2F4F7"/>' if header and ridx == 0 else ""
            bold = ridx == 0 and header
            xml.append(
                f'<w:tc><w:tcPr><w:tcW w:w="{widths[cidx]}" w:type="dxa"/>{fill}'
                '</w:tcPr>'
                + p(cell, bold=bold)
                + "</w:tc>"
            )
        xml.append("</w:tr>")
    xml.append("</w:tbl>")
    return "".join(xml)


styles = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal">
    <w:name w:val="Normal"/>
    <w:pPr><w:spacing w:after="120" w:line="264" w:lineRule="auto"/></w:pPr>
    <w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="22"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Title">
    <w:name w:val="Title"/>
    <w:pPr><w:spacing w:after="80"/><w:jc w:val="center"/></w:pPr>
    <w:rPr><w:b/><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="32"/><w:color w:val="0B2545"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Subtitle">
    <w:name w:val="Subtitle"/>
    <w:pPr><w:spacing w:after="160"/><w:jc w:val="center"/></w:pPr>
    <w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="24"/><w:color w:val="555555"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading1">
    <w:name w:val="heading 1"/>
    <w:basedOn w:val="Normal"/>
    <w:pPr><w:spacing w:before="240" w:after="120"/><w:keepNext/></w:pPr>
    <w:rPr><w:b/><w:sz w:val="32"/><w:color w:val="2E74B5"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading2">
    <w:name w:val="heading 2"/>
    <w:basedOn w:val="Normal"/>
    <w:pPr><w:spacing w:before="160" w:after="80"/><w:keepNext/></w:pPr>
    <w:rPr><w:b/><w:sz w:val="26"/><w:color w:val="2E74B5"/></w:rPr>
  </w:style>
</w:styles>
"""

numbering = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:abstractNum w:abstractNumId="1">
    <w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="bullet"/><w:lvlText w:val="-"/>
      <w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr></w:lvl>
  </w:abstractNum>
  <w:num w:numId="1"><w:abstractNumId w:val="1"/></w:num>
  <w:abstractNum w:abstractNumId="2">
    <w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="decimal"/><w:lvlText w:val="%1."/>
      <w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr></w:lvl>
  </w:abstractNum>
  <w:num w:numId="2"><w:abstractNumId w:val="2"/></w:num>
</w:numbering>
"""

body = []
body.append(p("STMIK ANTAR BANGSA", "Title"))
body.append(p("SOAL PRAKTIK UTS", "Title"))
body.append(p("Pemrograman Web Lanjut", "Subtitle"))
body.append(
    table(
        [
            ["Dosen", "Amirudin"],
            ["Project Acuan", "akademik-app_pert7"],
            ["Framework", "Laravel 13, Blade, Bootstrap 5, Eloquent ORM"],
            ["Durasi", "120 menit"],
            ["Sifat Ujian", "Praktik individu, open project, tanpa plagiarisme"],
        ],
        [2400, 6960],
        header=False,
    )
)
body.append(p("Deskripsi Ujian", "Heading1"))
body.append(
    p(
        "Kerjakan pengembangan aplikasi akademik berbasis Laravel sesuai project akademik-app_pert7. "
        "Hasil minimal harus memiliki tampilan dan alur yang setara dengan project acuan: navbar biru, layout card Bootstrap, "
        "tabel daftar mahasiswa, tombol filter jurusan, form tambah mahasiswa, dan halaman detail mahasiswa."
    )
)
body.append(p("Kompetensi yang Dinilai", "Heading1"))
for item in [
    "Memahami route, controller, model, migration, seeder, dan Blade view pada Laravel.",
    "Menggunakan relasi Eloquent: Jurusan memiliki banyak Mahasiswa, Mahasiswa memiliki satu KartuMahasiswa, dan Mahasiswa memiliki banyak MataKuliah melalui tabel Nilai.",
    "Membuat validasi input dan menampilkan pesan error pada form.",
    "Menampilkan data relasional pada halaman index dan detail dengan tampilan Bootstrap yang rapi.",
    "Mengembangkan fitur tambahan dari halaman detail mahasiswa.",
]:
    body.append(bullet(item))

body.append(p("Ketentuan Pengerjaan", "Heading1"))
for item in [
    "Gunakan project akademik-app_pert7 sebagai acuan struktur dan tampilan.",
    "Boleh menambah route, controller method, model, migration, seeder, dan view sesuai kebutuhan.",
    "Database harus dapat dijalankan melalui migration dan seeder, atau menggunakan database sqlite yang sudah tersedia.",
    "Nama tabel, relasi, dan field boleh mengikuti project acuan agar mudah diperiksa.",
    "Tampilan minimal harus sama rapi dengan project acuan: card shadow, header berwarna, table bordered/striped, badge mata kuliah, dan tombol aksi.",
    "Setiap fitur harus dapat diuji dari browser, bukan hanya dibuat di kode.",
]:
    body.append(numbered(item))

body.append(p("Tugas Wajib", "Heading1"))
body.append(p("1. Setup dan Struktur Project (10 poin)", "Heading2"))
for item in [
    "Jalankan aplikasi Laravel dan pastikan halaman utama, about, dan list mahasiswa dapat diakses.",
    "Pastikan route /mahasiswa, /mahasiswa/create, /mahasiswa/detail/{id}, /mahasiswa/jurusan/{kode}, dan /statistik-jurusan tersedia atau ekuivalen.",
    "Pastikan tabel jurusans, mahasiswas, kartu_mahasiswas, mata_kuliahs, dan nilais tersedia.",
]:
    body.append(bullet(item))

body.append(p("2. Halaman List Mahasiswa (15 poin)", "Heading2"))
for item in [
    "Tampilkan tabel berisi No, NIM, Nama, Jurusan, No Kartu, Mata Kuliah, dan Aksi.",
    "Gunakan eager loading untuk menampilkan jurusan, kartu mahasiswa, dan mata kuliah beserta nilai.",
    "Mata kuliah dan nilai ditampilkan dalam bentuk badge atau tampilan lain yang setara dan mudah dibaca.",
    "Sediakan tombol Semua, TI, SI, dan MI untuk filter mahasiswa berdasarkan kode jurusan.",
    "Sediakan tombol Detail pada setiap baris mahasiswa.",
]:
    body.append(bullet(item))

body.append(p("3. Form Tambah Mahasiswa (15 poin)", "Heading2"))
for item in [
    "Buat form tambah mahasiswa dengan field Jurusan, NIM, Nama, Email, Jenis Kelamin, dan Alamat.",
    "Gunakan validasi: jurusan wajib ada, NIM unik, email valid dan unik, nama wajib, jenis kelamin hanya L atau P.",
    "Jika validasi gagal, tampilkan pesan error pada form.",
    "Jika berhasil, redirect ke list mahasiswa dan tampilkan pesan sukses.",
]:
    body.append(bullet(item))

body.append(p("4. Halaman Detail Mahasiswa (15 poin)", "Heading2"))
for item in [
    "Tampilkan biodata mahasiswa: NIM, Nama, Email, Jenis Kelamin, Alamat, Jurusan, dan No Kartu.",
    "Tampilkan tabel nilai mata kuliah berisi Kode MK, Mata Kuliah, SKS, Nilai, dan Aksi.",
    "Sediakan tombol Kembali ke halaman list mahasiswa.",
    "Data yang tampil harus berasal dari relasi Eloquent, bukan hard-code di Blade.",
]:
    body.append(bullet(item))

body.append(p("5. Relasi Model dan Query (15 poin)", "Heading2"))
for item in [
    "Mahasiswa belongsTo Jurusan.",
    "Mahasiswa hasOne KartuMahasiswa.",
    "Mahasiswa belongsToMany MataKuliah melalui tabel nilais dengan pivot nilai.",
    "MataKuliah belongsToMany Mahasiswa melalui tabel nilais.",
    "Gunakan with(), whereHas(), atau withCount() pada bagian yang sesuai.",
]:
    body.append(bullet(item))

body.append(p("6. Statistik Mahasiswa per Jurusan (10 poin)", "Heading2"))
for item in [
    "Buat halaman statistik yang menampilkan jumlah mahasiswa per jurusan.",
    "Gunakan withCount('mahasiswa') atau query relasi yang setara.",
    "Tampilkan setiap jurusan dalam card Bootstrap berisi nama jurusan, kode jurusan, dan jumlah mahasiswa.",
]:
    body.append(bullet(item))

body.append(p("7. Kerapian Tampilan dan Navigasi (10 poin)", "Heading2"))
for item in [
    "Gunakan layout Blade utama dan partial navbar.",
    "Navbar memiliki menu Home, About, dan List Mahasiswa dengan status active.",
    "Gunakan Bootstrap 5, card, table responsive, warna header, tombol, dan spacing yang konsisten.",
    "Tidak ada link utama yang error saat diklik.",
]:
    body.append(bullet(item))

body.append(page_break())
body.append(p("Poin Plus / Bonus", "Heading1"))
body.append(p("Bonus A. Generate Kartu Mahasiswa (10 poin)", "Heading2"))
for item in [
    "Pada halaman detail, jika mahasiswa belum memiliki kartu, tampilkan tombol Generate Kartu.",
    "Saat tombol diklik, sistem membuat record KartuMahasiswa untuk mahasiswa tersebut.",
    "Format nomor kartu disarankan: KTM-{NIM}.",
    "Isi tanggal_terbit dengan tanggal saat generate dan tanggal_berlaku empat tahun setelah tanggal terbit.",
    "Setelah berhasil, kembali ke halaman detail dan No Kartu langsung tampil.",
]:
    body.append(bullet(item))

body.append(p("Bonus B. Tambah Mata Kuliah dan Nilai dari Detail Mahasiswa (10 poin)", "Heading2"))
for item in [
    "Pada halaman detail, tombol Input Nilai Mata Kuliah harus membuka form input atau modal.",
    "Form minimal berisi pilihan Mata Kuliah dan input Nilai.",
    "Simpan data ke tabel nilais sebagai relasi mahasiswa dengan mata kuliah.",
    "Cegah duplikasi mata kuliah yang sama untuk mahasiswa yang sama.",
    "Setelah disimpan, nilai tampil pada tabel detail dan badge mata kuliah di halaman list mahasiswa.",
]:
    body.append(bullet(item))

body.append(p("Rubrik Penilaian", "Heading1"))
body.append(
    table(
        [
            ["Komponen", "Poin"],
            ["Setup, route, migration/seeder, dan struktur project", "10"],
            ["Halaman list mahasiswa lengkap dengan relasi dan filter", "15"],
            ["Form tambah mahasiswa, validasi, dan pesan sukses/error", "15"],
            ["Halaman detail mahasiswa dan tabel nilai", "15"],
            ["Relasi model dan query Eloquent yang benar", "15"],
            ["Statistik mahasiswa per jurusan", "10"],
            ["Kerapian tampilan dan navigasi", "10"],
            ["Bonus generate kartu mahasiswa", "+10"],
            ["Bonus input mata kuliah dan nilai dari detail", "+10"],
            ["Total maksimal", "100 + 20 bonus"],
        ],
        [7200, 2160],
    )
)

body.append(p("Format Pengumpulan", "Heading1"))
for item in [
    "Kumpulkan folder project Laravel atau repository yang dapat dijalankan.",
    "Sertakan database/migration/seeder yang dibutuhkan untuk menguji fitur.",
    "Sertakan screenshot halaman list, tambah mahasiswa, detail mahasiswa, statistik, dan fitur bonus jika dikerjakan.",
    "Pastikan file .env tidak berisi kredensial pribadi yang tidak perlu.",
]:
    body.append(numbered(item))

body.append(p("Catatan Pemeriksaan", "Heading1"))
body.append(
    p(
        "Nilai utama diberikan pada fitur yang berjalan. Tampilan yang hanya dibuat secara statis tanpa query database dan relasi Eloquent tidak mendapatkan poin penuh. "
        "Poin plus diberikan jika fitur bonus benar-benar dapat dijalankan dari tombol pada halaman detail mahasiswa."
    )
)

document = f"""<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
    {''.join(body)}
    <w:sectPr>
      <w:pgSz w:w="12240" w:h="15840"/>
      <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/>
    </w:sectPr>
  </w:body>
</w:document>
"""

content_types = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
  <Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
"""

rels = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
"""

doc_rels = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/>
</Relationships>
"""

core = f"""<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>Soal Praktik UTS Pemrograman Web Lanjut</dc:title>
  <dc:creator>Amirudin</dc:creator>
  <dc:subject>Praktik UTS Laravel Akademik</dc:subject>
  <dc:description>Soal praktik UTS berdasarkan project akademik-app_pert7 STMIK ANTAR BANGSA.</dc:description>
  <dcterms:created xsi:type="dcterms:W3CDTF">{date.today().isoformat()}T00:00:00Z</dcterms:created>
</cp:coreProperties>
"""

app = """<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Codex</Application>
</Properties>
"""

with ZipFile(OUT, "w", ZIP_DEFLATED) as z:
    z.writestr("[Content_Types].xml", content_types)
    z.writestr("_rels/.rels", rels)
    z.writestr("word/_rels/document.xml.rels", doc_rels)
    z.writestr("word/document.xml", document)
    z.writestr("word/styles.xml", styles)
    z.writestr("word/numbering.xml", numbering)
    z.writestr("docProps/core.xml", core)
    z.writestr("docProps/app.xml", app)

print(OUT.resolve())
