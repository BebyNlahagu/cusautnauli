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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('components/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('components/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{ asset('components/vendor/aos/aos.css')}}" rel="stylesheet">
    <link href="{{ asset('components/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
    <link href="{{ asset('components/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('components/css/main.css')}}" rel="stylesheet">

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
            <a class="btn-getstarted flex-md-shrink-0" data-bs-target="#add" data-bs-toggle="modal" style="cursor:pointer;">Daftar</a>

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

        <div class="modal fade" id="add" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Isi Form Pendaftaran Di Bawah Ini Dengan Lengkap</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('addNasabah') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="modal-body">
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" />
                                    <label for="floatingInput">Nama Lengkap</label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="number" class="form-control @error('Nik') is-invalid @enderror" id="Nik" name="Nik" min="0" oninput="if(this.value.length > 16) this.value = this.value.slice(0,16);" placeholder="Nomor NIK" value="{{ old('Nik') }}" />
                                    <span id="hasil"></span>
                                    <label for="floatingInput">Nomor NIK</label>
                                    @error('Nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
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
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" placeholder="Tanggal Lahir" value="{{ old('tanggal_lahir') }}" />
                                    <label for="floatingInput">Tanggal Lahir</label>
                                    @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="number" name="no_telp" class="form-control  @error('no_telp') is-invalid @enderror" min="0" oninput="if(this.value.length > 12) this.value = this.value.slice(0,12);" id="No_telp" placeholder="No. Hp/Wa" value="{{ old('no_telp') }}" />
                                    <span id="h"></span>
                                    <label for="floatingInput">No. Hp/Wa</label>
                                    @error('no_telp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- <div class="form-floating form-floating-custom mb-3">
                                    <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" cols="30" rows="3">{{ old('alamat') }}</textarea>
                                    <label for="floatingInput">Alamat</label>
                                    @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}

                                <div class="form-floating form-floating-custom mb-3">
                                    <select name="alamat_id" id="alamat_id" class="form-control @error('alamat_id') is-invalid @enderror">
                                        <option value="">-pilih-</option>
                                        @foreach ($alamat as $a)
                                            <option value="{{ $a->id }}">{{ $a->alamat }}</option>
                                        @endforeach
                                    </select>
                                    <label for="floatingInput">Alamat</label>
                                    @error('alamat_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" name="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan" placeholder="Kelurahan" value="{{ old('kelurahan') }}" />
                                    <label for="floatingInput">Kelurahan</label>
                                    @error('kelurahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" placeholder="Jenis Usaha" value="{{ old('pekerjaan') }}" />
                                    <label for="floatingInput">Pekerjaan</label>
                                    @error('pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>
                                <h5>Email Dan Password</h5>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="email" value="{{ old('email') }}" />
                                    <label for="floatingInput">Email</label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="password" value="{{ old('password') }}" />
                                    <label for="floatingInput">Password</label>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <hr>
                                <h5>Dokumen Pendukung</h5>
                                <hr>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" id="foto" placeholder="Jenis Usaha" value="{{ old('foto') }}" />
                                    <label for="floatingInput">Foto Diri</label>
                                    <img id="fotoPreview" src="#" alt="Foto Preview" style="max-width: 200px; margin-top: 10px; display: none;" />
                                    @error('foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="file" name="ktp" class="form-control @error('ktp') is-invalid @enderror" id="ktp" placeholder="Jenis Usaha" value="{{ old('ktp') }}" />
                                    <label for="floatingInput">KTP</label>
                                    <img id="ktpPreview" src="#" alt="KTP Preview" style="max-width: 200px; margin-top: 10px; display: none;" />
                                    @error('ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="file" name="kk" class="form-control @error('kk') is-invalid @enderror" id="kk" placeholder="Jenis Usaha" value="{{ old('kk') }}" />
                                    <label for="floatingInput">Kartu Keluarga</label>
                                    <img id="kkPreview" src="#" alt="KK Preview" style="max-width: 200px; margin-top: 10px; display: none;" />
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
        @if(session('success'))
        <script>
            Swal.fire({
                title: "Berhasil!"
                , text: "{{ session('success') }}"
                , icon: "success"
                , confirmButtonText: "OK"
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
                            icon: 'warning'
                            , title: 'Oops...'
                            , text: 'Tanggal lahir harus diisi'
                        , });
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
                            icon: 'error'
                            , title: 'Gagal'
                            , text: 'Umur harus minimal 17 tahun'
                        , });
                    }
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
                        {{-- <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
              <a href="#about" class="btn-get-started"> <i class="bi bi-arrow-right"></i></a>
              <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox btn-watch-video d-flex align-items-center justify-content-center ms-0 ms-md-4 mt-4 mt-md-0"><i class="bi bi-play-circle"></i><span>Watch Video</span></a>
            </div> --}}
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                        <img src="components/img/hero-img.png" class="img-fluid animated" alt="">
                    </div>
                </div>
            </div>

        </section><!-- /Hero Section -->

        <!-- About Section 
        <section id="about" class="about section">

            {{-- <div class="container" data-aos="fade-up">
                <div class="row gx-0">

                    <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                            <h3>Cu Saut Jaya Nauli</h3>
                            <h2>Expedita voluptas omnis cupiditate totam eveniet nobis sint iste. Dolores est repellat corrupti reprehenderit.</h2>
                            <p>
                                Quisquam vel ut sint cum eos hic dolores aperiam. Sed deserunt et. Inventore et et dolor consequatur itaque ut voluptate sed et. Magnam nam ipsum tenetur suscipit voluptatum nam et est corrupti.
                            </p>
                            <div class="text-center text-lg-start">
                                {{-- <a href="#" class="btn-read-more d-inline-flex align-items-center justify-content-center align-self-center">
                  <span>Read More</span>
                  <i class="bi bi-arrow-right"></i>
                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
                        <img src="components/img/about.jpg" class="img-fluid" alt="">
                    </div>

                </div>
            </div> --}}

        </section>/About Section -->


        <!-- Services Section 
        <section id="services" class="services section">

            
            <div class="container section-title" data-aos="fade-up">
                <h2>Layanan</h2>
                <p>Lihat Layanan Kami<br></p>
            </div><End Section Title -->

        {{-- <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item item-cyan position-relative">
                            <i class="bi bi-activity icon"></i>
                            <h3>Simpan Uang</h3>
                            <p>Provident nihil minus qui consequatur non omnis maiores. Eos accusantium minus dolores iure perferendis tempore et consequatur.</p>
                            <a href="#" class="read-more stretched-link"><span>Read More</span> <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item item-orange position-relative">
                            <i class="bi bi-broadcast icon"></i>
                            <h3>Pinjam Uang</h3>
                            <p>Ut autem aut autem non a. Sint sint sit facilis nam iusto sint. Libero corrupti neque eum hic non ut nesciunt dolorem.</p>
                            <a href="#" class="read-more stretched-link"><span>Read More</span> <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item item-teal position-relative">
                            <i class="bi bi-easel icon"></i>
                            <h3>Angsuran</h3>
                            <p>Ut excepturi voluptatem nisi sed. Quidem fuga consequatur. Minus ea aut. Vel qui id voluptas adipisci eos earum corrupti.</p>
                            <a href="#" class="read-more stretched-link"><span>Read More</span> <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div><!-- End Service Item -->
                </div>

            </div> --}}

        {{-- </section>/Services Section --> --}}
    </main>

    {{-- <footer id="footer" class="footer">

        {{-- <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="index.html" class="d-flex align-items-center">
                        <span class="sitename">FlexStart</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>A108 Adam Street</p>
                        <p>New York, NY 535022</p>
                        <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
                        <p><strong>Email:</strong> <span>info@example.com</span></p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">About us</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Services</a></li>
                    </ul>
                </div>

            </div>
        </div> --}}

    {{-- <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">CU Saut Jaya Nauli</strong> <span>All Rights Reserved</span></p>
            <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you've purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
            </div>
        </div> --}}

    {{-- </footer> --}}

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->


    <script src="{{asset('components/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('components/vendor/php-email-form/validate.js')}}"></script>
    <script src="{{asset('components/vendor/aos/aos.js')}}"></script>
    <script src="{{asset('components/vendor/glightbox/js/glightbox.min.js')}}"></script>
    <script src="{{asset('components/vendor/purecounter/purecounter_vanilla.js')}}"></script>
    <script src="{{asset('components/vendor/imagesloaded/imagesloaded.pkgd.min.js')}}"></script>
    <script src="{{asset('components/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
    <script src="{{asset('components/vendor/swiper/swiper-bundle.min.js')}}"></script>

    <!-- Main JS File -->
    <script src="{{asset('components/js/main.js')}}"></script>
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
