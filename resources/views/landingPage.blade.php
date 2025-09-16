<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>CU Saut Maut Nauli</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon" />
    <link href="{{ asset('img/logo.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('components/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('components/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('components/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('components/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('components/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('components/css/main.css') }}" rel="stylesheet">

    <!-- =======================================================
  * Template Name: FlexStart
  * Template URL: https://bootstrapmade.com/flexstart-bootstrap-startup-template/
  * Updated: Nov 01 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="index.html" class="logo d-flex align-items-center me-auto">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <img src="{{ asset('img/logo.png') }}" alt="">
                <h1 class="sitename">CU Saut Maju Nauli</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                {{-- <ul>
                    <li><a href="#hero" class="active">Home<br></a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                </ul> --}}
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted flex-md-shrink-0" href="{{ route('login') }}">Login</a>
            <a class="btn-getstarted flex-md-shrink-0" data-bs-target="#add" data-bs-toggle="modal"
                style="cursor:pointer;">Daftar</a>

        </div>
    </header>

    <main class="main">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="modal fade" id="add" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Isi Form Pendaftaran Di Bawah Ini Dengan
                            Lengkap</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('addNasabah') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="modal-body">
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Nama Lengkap"
                                        value="{{ old('name') }}" />
                                    <label for="floatingInput">Nama Lengkap</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="number" class="form-control @error('Nik') is-invalid @enderror"
                                        id="Nik" name="Nik" min="0"
                                        oninput="if(this.value.length > 16) this.value = this.value.slice(0,16);"
                                        placeholder="Nomor NIK" value="{{ old('Nik') }}" />
                                    <span id="hasil"></span>
                                    <label for="floatingInput">Nomor NIK</label>
                                    @error('Nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <select name="jenis_kelamin" id="jenis_kelamin"
                                        class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                        <option value="">-pilih-</option>
                                        <option value="Laki-laki">Laki-Laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <label for="floatingInput">Jenis Kelamin</label>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="date"
                                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                        id="tanggal_lahir" name="tanggal_lahir" placeholder="Tanggal Lahir"
                                        value="{{ old('tanggal_lahir') }}" />
                                    <label for="floatingInput">Tanggal Lahir</label>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="number" name="no_telp"
                                        class="form-control  @error('no_telp') is-invalid @enderror" min="0"
                                        oninput="if(this.value.length > 12) this.value = this.value.slice(0,12);"
                                        id="No_telp" placeholder="No. Hp/Wa" value="{{ old('no_telp') }}" />
                                    <span id="h"></span>
                                    <label for="floatingInput">No. Hp/Wa</label>
                                    @error('no_telp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <select name="kecamatan" id="kecamatan"
                                        class="form-control @error('kecamatan') is-invalid @enderror" cols="30"
                                        rows="3">
                                        <option value="">--Pilih--</option>
                                        <option value="Kecamatan Siborong Borong">Kecamatan Siborong Borong</option>
                                        <option value="Kecamatan Paranginan">Kecamatan Paranginan</option>
                                        <option value="Kecamatan Lintong Nihuta">Kecamatan Lintong Nihuta</option>
                                    </select>
                                    <label for="floatingInput">Kecamatan</label>
                                    @error('kecamatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <select name="desa" id="desa"
                                        class="form-control @error('desa') is-invalid @enderror">
                                        {{-- <option value="">-- Pilih Desa --</option> --}}
                                    </select>
                                    <label for="floatingInput">Kelurahan</label>
                                    @error('desa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" name="kelurahan"
                                        class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan"
                                        placeholder="Kelurahan" value="{{ old('kelurahan') }}" />
                                    <label for="floatingInput">Alamat</label>
                                    @error('kelurahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" name="pekerjaan"
                                        class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan"
                                        placeholder="Jenis Usaha" value="{{ old('pekerjaan') }}" />
                                    <label for="floatingInput">Pekerjaan</label>
                                    @error('pekerjaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>
                                <h5>Username Dan Password</h5>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" name="username"
                                        class="form-control @error('username') is-invalid @enderror" id="username"
                                        placeholder="username" value="{{ old('username') }}" />
                                    <label for="floatingInput">Username</label>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" id="password"
                                        placeholder="password" value="{{ old('password') }}" />
                                    <label for="floatingInput">Password</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <hr>
                                <h5>Dokumen Pendukung</h5>
                                <hr>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="file" name="foto"
                                        class="form-control @error('foto') is-invalid @enderror" accept="image/*"
                                        capture="user" id="foto" placeholder="Jenis Usaha"
                                        value="{{ old('foto') }}" />
                                    <label for="floatingInput">Foto Diri</label>
                                    <img id="fotoPreview" src="#" alt="Foto Preview"
                                        style="max-width: 200px; margin-top: 10px; display: none;" />
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--
                                <div class="form-floating form-floating-custom mb-3">
                                    <video id="camera" autoplay playsinline
                                        style="width: 100%; max-width: 300px;"></video>
                                    <canvas id="snapshot" style="display:none;"></canvas>
                                    <input type="hidden" name="foto" id="fotoInput">
                                    <br>
                                    <button type="button" class="btn btn-primary" onclick="takeSnapshot()">📸 Ambil
                                        Foto</button>
                                    <img id="fotoPreview" src="#" alt="Foto Preview"
                                        style="max-width: 200px; margin-top: 10px; display:none;" />
                                </div> -->


                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="file" name="ktp"
                                        class="form-control @error('ktp') is-invalid @enderror" id="ktp"
                                        placeholder="Jenis Usaha" value="{{ old('ktp') }}" />
                                    <label for="floatingInput">KTP</label>
                                    <img id="ktpPreview" src="#" alt="KTP Preview"
                                        style="max-width: 200px; margin-top: 10px; display: none;" />
                                    @error('ktp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="file" name="kk"
                                        class="form-control @error('kk') is-invalid @enderror" id="kk"
                                        placeholder="Jenis Usaha" value="{{ old('kk') }}" />
                                    <label for="floatingInput">Kartu Keluarga</label>
                                    <img id="kkPreview" src="#" alt="KK Preview"
                                        style="max-width: 200px; margin-top: 10px; display: none;" />
                                    @error('kk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Daftar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        @if (session('success'))
            <script>
                Swal.fire({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            </script>
        @endif

        @if (session('swal_error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: '{{ session('swal_error') }}',
                    confirmButtonColor: '#d33'
                });
            </script>
        @endif

        <script>
            $(document).ready(function() {
                $("#Nik").on("input", function() {
                    var input = $(this).val();
                    var regex = /^[0-9]{0,16}$/;

                    if (regex.test(input)) {
                        $('#hasil').text("");
                    } else {
                        $('#hasil').text("");
                    }
                })

                $("#No_telp").on("input", function() {
                    var input = $(this).val();
                    var regex = /^08[0-9]{8,12}$/;

                    if (regex.test(input)) {
                        $("#h").text("");
                    } else {
                        $("#h").text("");
                    }
                })

                $('form').on('submit', function(e) {
                    var tanggalLahirVal = $('#tanggal_lahir').val();
                    if (!tanggalLahirVal) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Tanggal lahir harus diisi',
                        });
                        return;
                    }

                    var tanggalLahir = new Date(tanggalLahirVal);
                    var today = new Date();
                    var ageDifMs = today - tanggalLahir;
                    var ageDate = new Date(ageDifMs);
                    var age = Math.abs(ageDate.getUTCFullYear() - 1970);

                    if (age < 17) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Umur harus minimal 17 tahun',
                        });
                    }
                });

                const desaMap = {
                    "Kecamatan Paranginan": [
                        "Desa Paranginan Selatan", "Desa Siborutorop", "Desa Lumban Sialaman",
                        "Desa Lumban Barat", "Desa Lobu Tolong", "Desa Sihonongan", "Desa Paranginan Utara",
                        "Desa Pearung", "Desa Paerung Silali", "Desa Lumban Sianturi",
                        "Desa Lobutolong Habinsaran"
                    ],
                    "Kecamatan Lintong Nihuta": [
                        "Desa Nagasaribu I", "Desa Nagasaribu II", "Desa Nagasaribu III", "Desa Nagasaribu IV",
                        "Desa Nagasaribu V", "Desa Sigompul", "Desa Pargaulan"
                    ],
                    "Kecamatan Siborong Borong": [
                        "Desa Siborong Borong", "Desa Sitampurung", "Desa Sigalingging"
                    ]
                };


                $('#kecamatan').on('change', function() {
                    const selectedKec = $(this).val();
                    const desaList = desaMap[selectedKec] || [];

                    $('#desa').html('<option value="">-- Pilih Desa --</option>');
                    desaList.forEach(function(desa) {
                        $('#desa').append(`<option value="${desa}">${desa}</option>`);
                    });
                });
            })
        </script>


        <!-- Hero Section -->
        <section id="hero" class="hero section">

            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                        <h1 data-aos="fade-up">Selamat Datang Di Cu Saut Maju Nauli</h1>
                        <p data-aos="fade-up" data-aos-delay="100">Kami Siap Melayani Kebutuhan Anda</p>
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                        <img src="components/img/hero-img.png" class="img-fluid animated" alt="">
                    </div>
                </div>
            </div>

        </section><!-- /Hero Section -->
    </main>


    {{-- </footer> --}}

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->


    <script src="{{ asset('components/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('components/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('components/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('components/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('components/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('components/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('components/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('components/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('components/js/main.js') }}"></script>
    <script>
        function validateImage(input, previewId, errorId) {
            const file = files.input[0];
            const tipe = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            const maxSize = 2 * 1024 * 1024;

            $(errorId).text('');
            $(previewId).attr('src', '');

            if (file) {
                if (!tipe.includes(file.type)) {
                    $(errorId).text('File harus berupa gambar (jpeg, png, jpg, webp).');
                    input.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    $(errorId).text('Ukuran file maksimal 2MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $(previewId).attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }

        $(document).ready(function() {
            // Preview for Foto Diri
            $('#foto').change(function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('#fotoPreview').attr('src', event.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Preview for KTP
            $('#ktp').change(function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('#ktpPreview').attr('src', event.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Preview for Kartu Keluarga
            $('#kk').change(function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('#kkPreview').attr('src', event.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });


        });
    </script>


</body>

</html>
