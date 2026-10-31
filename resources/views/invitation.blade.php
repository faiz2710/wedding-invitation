@php
    /*
    |--------------------------------------------------------------------------
    | PENGATURAN UNDANGAN
    |--------------------------------------------------------------------------
    | Satu tempat untuk data yang mudah diubah. Elemen tanpa data akan
    | menampilkan teks pengganti yang aman (tanpa mengarang informasi).
    */
    $musicSrc = asset('audio/Akad.mp3');

    // Tanggal hari pernikahan (target countdown).
    $weddingDateIso = '2026-11-28T00:00:00+07:00';

    /*
    | Contoh jadwal — WAJIB diperbarui kalau waktu pasti sudah ditentukan.
    | venue/map kosong = tombol Maps tidak tampil dan kartu memakai
    | teks "Segera diinformasikan".
    */
    $akad = [
        'time'    => '09.00 WIB — selesai',
        'start'   => '2026-11-28T09:00:00+07:00',
        'end'     => '2026-11-28T10:00:00+07:00',
        'venue'   => 'Gedung SMK Al Basyariah',
        'address' => 'Jl. Raya Pabuaran Rawa Panjang, Kec. Bojonggede, Kab. Bogor, Jawa Barat',
        'map'     => 'https://share.google/sWNRAqQbc9p7dVnlz',
    ];

    $resepsi = [
        'time'    => '10.00–16.00 WIB',
        'start'   => '2026-11-28T10:00:00+07:00',
        'end'     => '2026-11-28T16:00:00+07:00',
        'venue'   => 'Gedung SMK Al Basyariah',
        'address' => 'Jl. Raya Pabuaran Rawa Panjang, Kec. Bojonggede, Kab. Bogor, Jawa Barat',
        'map'     => 'https://share.google/sWNRAqQbc9p7dVnlz',
    ];

    // Status halaman: 'rsvp' | 'wishes' | 'none'
    $pageState = (session('rsvp_success') || $errors->any())
        ? 'rsvp'
        : ((session('wishes_success') || $errors->wishes->any()) ? 'wishes' : 'none');

    /*
    | Kartu rekening (kado digital) — kedua mempelai.
    | Nomor masih kosong = tampil "segera diinformasikan" tanpa tombol salin.
    */
    $bank = [
        'accounts' => [
            [
                'holder' => 'Alif Kusuma Ramadhan',
                'bank'   => 'BCA',
                'number' => '8692006690',
            ],
            [
                'holder' => 'Indi Ariani',
                'bank'   => 'BCA',
                'number' => '7651095127',
            ],
        ],
    ];

@endphp

<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#431322">
    <meta name="description" content="Dengan penuh cinta, Alif dan Indy mengundang Anda untuk hadir di hari bahagia kami.">

    <title>Wedding Invitation — Alif &amp; Indy</title>

    <script>
        document.documentElement.classList.replace('no-js', 'js');
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Great+Vibes&family=DM+Sans:wght@400;500;600;700&family=Monsieur+La+Doulaise&family=Scheherazade+New:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/invitation.css') }}">
</head>

<body data-rsvp-state="{{ $pageState }}">

    {{-- SVG ICON --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <defs>
            <symbol id="i-down" viewBox="0 0 24 24">
                <path d="M6 9l6 6 6-6"/>
            </symbol>
            <symbol id="i-home" viewBox="0 0 24 24">
                <path d="M3 11l9-7 9 7M5 10v10h14V10M10 20v-6h4v6"/>
            </symbol>
            <symbol id="i-cal" viewBox="0 0 24 24">
                <rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/>
            </symbol>
            <symbol id="i-image" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.8"/><path d="M21 16l-5-5-8 8"/>
            </symbol>
            <symbol id="i-mail" viewBox="0 0 24 24">
                <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>
            </symbol>
            <symbol id="i-note" viewBox="0 0 24 24">
                <path d="M9 18V6l10-2v12"/><circle cx="7" cy="18" r="2.5"/><circle cx="17" cy="16" r="2.5"/>
            </symbol>
            <symbol id="i-pin" viewBox="0 0 24 24">
                <path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>
            </symbol>
            <symbol id="i-plus-cal" viewBox="0 0 24 24">
                <rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4M12 13v5M9.5 15.5h5"/>
            </symbol>
            <symbol id="i-copy" viewBox="0 0 24 24">
                <rect x="9" y="9" width="11" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>
            </symbol>
        </defs>
    </svg>

    {{-- =========================================
         OPENING — AMPLOP UNDANGAN
         Gambar yang dipakai:
         images/background-welcome.jpg  (latar damask + renda)
         images/bunga-back.png          (bunga dasar halaman)
         images/bunga-atas.png          (bunga kiri-atas amplop)
         images/bunga-bawah.png         (bunga kanan-bawah amplop)
         images/bulat.png               (wax seal)
    ========================================== --}}
    <div
        class="opening"
        id="opening"
        role="dialog"
        aria-modal="true"
        aria-label="Pembuka undangan"
    >

        {{-- Bunga dasar halaman --}}
        <img
            src="{{ asset('images/bunga-back.png') }}"
            class="opening__flower-back"
            alt=""
            aria-hidden="true"
        >

        {{-- Lewati animasi (pojok kanan atas) --}}
        <button
            type="button"
            class="opening__skip"
            id="skipOpening"
        >
            Lewati animasi
        </button>

        <div class="opening__stage">

            {{-- Judul --}}
            <div class="opening__heading">
                <p class="opening__kicker">The wedding of</p>
                <p class="opening__names">Alif &amp; Indy</p>
                <p class="opening__date">28.11.2026</p>
            </div>

            {{-- Amplop --}}
            <div class="envelope" id="envelope">

                <div class="envelope__back"></div>

                <div class="envelope__letter">
                    <p class="envelope__kicker">
                        The wedding of
                    </p>

                    <p class="envelope__names">
                        Alif <span>&amp;</span> Indy
                    </p>

                    <p class="envelope__date">
                        28 November 2026
                    </p>
                </div>

                <div class="envelope__front"></div>

                <div class="envelope__flap"></div>

                <button
                    type="button"
                    class="envelope__seal"
                    id="sealButton"
                    aria-label="Buka undangan"
                >
                    <span>A<small>&amp;</small>I</span>
                </button>

                {{-- Bunga menempel di amplop --}}
                <img
                    src="{{ asset('images/bunga-atas.png') }}"
                    class="envelope__flower envelope__flower--top"
                    alt=""
                    aria-hidden="true"
                >

                <img
                    src="{{ asset('images/bunga-bawah.png') }}"
                    class="envelope__flower envelope__flower--bottom"
                    alt=""
                    aria-hidden="true"
                >

            </div>

            {{-- Nama tamu --}}
            <p class="opening__to">
                Kepada Yth.
                <strong id="openingGuest">Tamu Undangan</strong>
            </p>

            {{-- Tombol --}}
            <div class="opening__actions">
                <button
                    type="button"
                    class="button"
                    id="openInvitation"
                >
                    Buka undangan
                </button>
            </div>

        </div>
    </div>

    {{-- =========================================
         HALAMAN UTAMA — HERO
    ========================================== --}}
    <main class="invitation" id="main">

        {{-- =========================================
             HERO AYAT — kartu renda, bunga, lace
             Gambar yang dipakai:
             images/background-hero.png  (latar damask burgundy)
             images/lace.png             (renda di tepi kanan)
             images/card-hro.png         (kartu oval + angsa)
             images/bunga-atas.png       (bunga di kiri-atas kartu)
        ========================================== --}}
        <section
            class="hero hero--ayat"
            id="ayat"
            aria-label="Ayat Al-Qur'an tentang pernikahan"
        >

            {{-- Renda di tepi kanan --}}
            
            <div class="ayat__stage">

                <figure class="ayat__card" data-intro style="--i:0">

                    <img
                        src="{{ asset('images/card-hro.png') }}"
                        class="ayat__card-img"
                        alt=""
                    >

                    {{-- Teks ayat (hapus blok ini jika card-hro.png sudah memuat tulisan) --}}
                    <div class="ayat__content">
                        <p class="ayat__arabic" lang="ar" dir="rtl">
                            وَمِنْ اٰيٰتِهٖٓ اَنْ خَلَقَ لَكُمْ مِّنْ اَنْفُسِكُمْ اَزْوَاجًا لِّتَسْكُنُوْٓا اِلَيْهَا وَجَعَلَ بَيْنَكُمْ مَّوَدَّةً وَّرَحْمَةً ۗ اِنَّ فِيْ ذٰلِكَ لَاٰيٰتٍ لِّقَوْمٍ يَّتَفَكَّرُوْنَ
                        </p>

                        <p class="ayat__translation">
                            Di antara tanda-tanda (kebesaran)-Nya ialah bahwa Dia menciptakan
                            pasangan-pasangan untukmu dari (jenis) dirimu sendiri agar kamu merasa
                            tenteram kepadanya. Dia menjadikan di antaramu rasa cinta dan kasih sayang.
                            Sesungguhnya pada yang demikian itu benar-benar terdapat tanda-tanda
                            (kebesaran Allah) bagi kaum yang berpikir.
                        </p>

                        <p class="ayat__source">QS Ar-Rum:21</p>
                    </div>

                    {{-- Bunga menempel di kartu --}}
                    <img
                        src="{{ asset('images/bunga-atas.png') }}"
                        class="ayat__flower"
                        alt=""
                        aria-hidden="true"
                    >

                </figure>

            </div>

        </section>

        <!-- =========================================
             SECTION PROFIL CALON MEMPELAI
             Gambar yang dipakai:
             - background-calon.png (Background serat/motif hijau)
             - card-calon.png       (Frame renda + bismillah + bunga + foto)
             - vas.png              (Pot/vas bunga di kiri bawah)
        ========================================== -->
        <section class="mempelai-section" id="mempelai" aria-label="Profil Calon Mempelai">
            
            <!-- Container Card Utama (Ungu) -->
            <div class="mempelai__card-wrapper">
                <img 
                    src="{{ asset('images/calon-merah.png') }}" 
                    alt="Kartu Mempelai Alif dan Indy" 
                    class="mempelai__card-img"
                >
            </div>

            <!-- Pot/Vas Bunga Kiri Bawah (Oren) -->
            <img 
                src="{{ asset('images/vas.png') }}" 
                alt="Vas Bunga Decorative" 
                class="mempelai__vas-img"
                aria-hidden="true"
            >

        </section>

        
        
        <!-- =========================================
             WEDDING DETAILS
             Data dari variabel akad & resepsi di atas.
        ========================================== -->
        <section class="section section--details" id="details" aria-labelledby="detailsTitle">
            <div class="section-heading" data-reveal="rise">
                <p class="eyebrow">Save the date</p>
                <h2 class="section-title" id="detailsTitle">The Wedding Day</h2>
                <p class="section__date">
                    <time datetime="2026-11-28"><span>Sabtu</span> · 28 · 11 · 2026</time>
                </p>
            </div>

            {{-- Data acara dipisah dari tampilan agar .ics tetap berisi 2 acara --}}
            <span data-event="akad" data-title="Akad Nikah — Alif &amp; Indy" data-start="{{ $akad['start'] }}" data-end="{{ $akad['end'] }}" hidden></span>
            <span data-event="resepsi" data-title="Resepsi — Alif &amp; Indy" data-start="{{ $resepsi['start'] }}" data-end="{{ $resepsi['end'] }}" hidden></span>

            <div class="details__stage" data-reveal="pop">

                <img
                    src="{{ asset('images/card-tanggal.png') }}"
                    alt=""
                    class="details__card-img"
                    aria-hidden="true"
                >

                {{-- Isi kartu: Akad & Resepsi jadi satu --}}
                <div class="details__content">

                    <article class="event event--akad">
                        <p class="event__label">The ceremony</p>
                        <h3 class="event__name">Akad Nikah</h3>
                        <p class="event__date">Sabtu, 28 November 2026</p>
                        <p class="event__time">{{ $akad['time'] }}</p>
                        <p class="event__venue">{{ $akad['venue'] ?? 'Segera diinformasikan' }}</p>
                        <p class="event__address">{{ $akad['address'] ?? 'Alamat lengkap menyusul' }}</p>
                    </article>

                    <div class="event__rule" aria-hidden="true"></div>

                    <article class="event event--resepsi">
                        <p class="event__label">The celebration</p>
                        <h3 class="event__name">Resepsi</h3>
                        <p class="event__date">Sabtu, 28 November 2026</p>
                        <p class="event__time">{{ $resepsi['time'] }}</p>
                      
                    </article>

                </div>

                {{-- Ornamen bunga menempel di kartu --}}
                <img
                    src="{{ asset('images/bunga-atas.png') }}"
                    class="details__flower details__flower--top"
                    alt=""
                    aria-hidden="true"
                >
                <img
                    src="{{ asset('images/bunga-bawah.png') }}"
                    class="details__flower details__flower--bottom"
                    alt=""
                    aria-hidden="true"
                >

            </div>

            <div class="details__actions" data-reveal="rise">
                <button type="button" class="button button--ghost" id="addToCalendar">
                    <svg class="icon" aria-hidden="true"><use href="#i-plus-cal"/></svg>
                    Simpan ke kalender
                </button>
            </div>
        </section>

        <!-- =========================================
             LOCATION — Alamat & Google Maps
             Diambil dari $akad['venue'/'address'/'map']
        ========================================== -->
        @php
            $locationUrl = $akad['map'] ?? ($akad['address']
                ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($akad['address'])
                : null);
        @endphp

        @if ($locationUrl)
            <section class="section section--location" id="lokasi" aria-labelledby="locationTitle">
                <div class="section-heading" data-reveal="rise">
                    <p class="eyebrow">Where it all happens</p>
                    <h2 class="section-title" id="locationTitle">Location</h2>
                </div>

                <div class="location__stage" data-reveal="pop">

                    <img
                        src="{{ asset('images/card-location.png') }}"
                        alt=""
                        class="location__card-img"
                        aria-hidden="true"
                    >

                    {{-- Isi kartu — di atas renda merah, warna paper --}}
                    <div class="location__content">
                        <h3 class="location__venue">
                            {{ $akad['venue'] ?? $akad['address'] ?? 'Tempat akan segera diinformasikan' }}
                        </h3>
                        <p class="location__address">
                            {{ $akad['address'] ?? 'Alamat lengkap akan segera diinformasikan' }}
                        </p>

                        <a class="button button--light location__button" href="{{ $locationUrl }}" target="_blank" rel="noopener">
                            <svg class="icon" aria-hidden="true"><use href="#i-pin"/></svg>
                            Buka di Google Maps
                        </a>
                    </div>



                </div>
            </section>
        @endif

        <!-- =========================================
             COUNTDOWN — menuju 28 November 2026
        ========================================== -->
        <section class="section section--countdown" id="countdown" aria-labelledby="countdownTitle">
            <div class="section-heading" data-reveal="rise">
                <p class="eyebrow">The countdown begins</p>
                <h2 class="section-title" id="countdownTitle">Until We Say "I Do"</h2>
            </div>

            <div class="countdown__stage" data-reveal="pop">

                <img
                    src="{{ asset('images/card-waktu.png') }}"
                    alt=""
                    class="countdown__card-img"
                    aria-hidden="true"
                >

                <div
                    class="countdown"
                    id="countdownTimer"
                    role="timer"
                    aria-label="Hitung mundur menuju hari pernikahan"
                    data-deadline="{{ $weddingDateIso }}"
                >
                    <div class="countdown__item">
                        <span id="days">000</span>
                        <small>Hari</small>
                    </div>
                    <span class="countdown__separator" aria-hidden="true">:</span>
                    <div class="countdown__item">
                        <span id="hours">00</span>
                        <small>Jam</small>
                    </div>
                    <span class="countdown__separator" aria-hidden="true">:</span>
                    <div class="countdown__item">
                        <span id="minutes">00</span>
                        <small>Menit</small>
                    </div>
                    <span class="countdown__separator" aria-hidden="true">:</span>
                    <div class="countdown__item">
                        <span id="seconds">00</span>
                        <small>Detik</small>
                    </div>
                </div>

            </div>

            <p class="section__note">Counting every moment until our forever begins.</p>
        </section>

        <!-- =========================================
             BANK — Kado Digital (nomor rekening)
             Data dari $bank['accounts'] di atas.
             Gambar: images/card-bank.png
        ========================================== -->
        <section class="section section--bank" id="kado" aria-labelledby="bankTitle">
            <div class="section-heading" data-reveal="rise">
                <p class="eyebrow">Wedding gift</p>
                <h2 class="section-title" id="bankTitle">Send a Gift</h2>
            </div>

            <div class="bank__stage" data-reveal="pop">

                <img
                    src="{{ asset('images/card-bank.png') }}"
                    alt=""
                    class="bank__card-img"
                    aria-hidden="true"
                >

                {{-- Isi kartu: rekening kedua mempelai --}}
                <div class="bank__content">
                    <h3 class="bank__heading">Amplop Digital</h3>

                    @foreach ($bank['accounts'] as $account)
                        <div class="bank__account">
                            <p class="bank__holder">{{ $account['holder'] }}</p>

                            @if ($account['bank'])
                                <p class="bank__name">{{ $account['bank'] }}</p>
                            @endif

                            @if ($account['number'])
                                <p class="bank__number">{{ $account['number'] }}</p>
                                <button
                                    type="button"
                                    class="button button--ghost bank__copy"
                                    data-copy="{{ $account['number'] }}"
                                >
                                    <svg class="icon" aria-hidden="true"><use href="#i-copy"/></svg>
                                    Salin nomor rekening
                                </button>
                            @else
                                <p class="bank__number bank__number--empty">Segera diinformasikan</p>
                            @endif
                        </div>
                    @endforeach
                </div>



            </div>
        </section>

        <!-- =========================================
             OUR MOMENTS — galeri film & scrapbook
             Foto lokal: hero-photo.PNG, alif.jpeg, indy.jpeg,
             duduk.jpeg, berdiri.jpeg
        ========================================== -->
        <section class="section section--moments" id="moments" aria-labelledby="momentsTitle">
            <div class="section-heading" data-reveal="rise">
                <p class="eyebrow">Our moments</p>
                <h2 class="section-title" id="momentsTitle">Captured in Film</h2>
                <p class="section__subtitle">Little scenes of us, printed on paper.</p>
            </div>

            {{-- Slideshow momen — card scrapbook diganti slide foto.
                 Foto pertama = hero-photo, lalu foto-foto mempelai --}}
            <div class="moments__slideshow" data-reveal="pop">

                <div
                    class="moment-slides"
                    id="momentSlides"
                    role="group"
                    aria-roledescription="carousel"
                    aria-label="Galeri momen Alif dan Indy"
                >
                    <figure class="moment-slide is-active">
                        <img src="{{ asset('images/photo-lace.png') }}" alt="Alif dan Indy berpose berdua">
                    </figure>
                </div>

            </div>
        </section>

        <!-- =========================================
             RSVP — konfirmasi kehadiran
             Backend sudah ada: POST /rsvp → RsvpController
        ========================================== -->
        <section class="section section--rsvp" id="rsvp" aria-labelledby="rsvpTitle">
            <div class="paper-card" data-reveal="rise">
                <span class="paper-card__tape" aria-hidden="true"></span>
                <img src="{{ asset('images/bunga-atas.png') }}" class="paper-card__flower" alt="" aria-hidden="true">

                <p class="eyebrow">We would love to see you</p>
                <h2 class="section-title" id="rsvpTitle">Will You Join Us?</h2>
                <p class="paper-card__intro">
                    Kehadiranmu akan menjadi hadiah yang berarti bagi kami.
                    Mohon luangkan waktu untuk mengisi konfirmasi berikut.
                </p>

                @if (session('rsvp_success'))
                    <div class="form-alert form-alert--success" role="status" data-feedback="rsvp" tabindex="-1">
                        <p class="form-alert__title">Terima kasih</p>
                        <p>{{ session('rsvp_success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="form-alert form-alert--error" role="alert" data-feedback="rsvp" tabindex="-1">
                        <p class="form-alert__title">Periksa kembali formulirmu:</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('rsvp.store') }}" method="POST" class="invite-form" id="rsvpForm">
                    @csrf

                    <div class="invite-form__group">
                        <label for="name">Nama lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', request('to')) }}"
                            placeholder="Masukkan nama lengkap"
                            maxlength="100"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <div class="invite-form__group">
                        <label for="attendance">Konfirmasi kehadiran</label>
                        <select id="attendance" name="attendance" required>
                            <option value="">Pilih konfirmasi</option>
                            <option value="hadir" {{ old('attendance') === 'hadir' ? 'selected' : '' }}>
                                Ya, saya akan hadir
                            </option>
                            <option value="tidak_hadir" {{ old('attendance') === 'tidak_hadir' ? 'selected' : '' }}>
                                Maaf, saya tidak bisa hadir
                            </option>
                        </select>
                    </div>

                    <div class="invite-form__group">
                        <label for="guests">Jumlah orang</label>
                        <div class="stepper">
                            <button type="button" class="stepper__btn" data-step="-1" aria-label="Kurangi jumlah orang">&minus;</button>
                            <input
                                type="number"
                                id="guests"
                                name="guests"
                                value="{{ old('guests', 1) }}"
                                min="1"
                                max="10"
                                inputmode="numeric"
                                aria-describedby="guestsHint"
                                required
                            >
                            <button type="button" class="stepper__btn" data-step="1" aria-label="Tambah jumlah orang">+</button>
                        </div>
                        <small id="guestsHint">Maksimal 10 orang per konfirmasi.</small>
                    </div>

                    <div class="invite-form__group">
                        <label for="message">Ucapan dan doa <span>(opsional)</span></label>
                        <textarea
                            id="message"
                            name="message"
                            rows="4"
                            maxlength="1000"
                            placeholder="Tuliskan ucapan terbaikmu..."
                        >{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="button button--submit" data-label="Kirim Konfirmasi">
                        <span class="button__label">Kirim Konfirmasi</span>
                        <svg class="icon" aria-hidden="true"><use href="#i-mail"/></svg>
                    </button>
                </form>
            </div>
        </section>

        <!-- =========================================
             WEDDING WISHES — ucapan & doa
             Backend: POST /wishes → WishController,
             daftar ucapan dari tabel wishes (terbaru dulu).
        ========================================== -->
        <section class="section section--wishes" id="wishes" aria-labelledby="wishesTitle">
            <div class="section-heading" data-reveal="rise">
                <p class="eyebrow">With love, they wrote</p>
                <h2 class="section-title" id="wishesTitle">Wedding Wishes</h2>
                <p class="section__subtitle">Tuliskan ucapan dan doa terbaik untuk kami.</p>
            </div>

            <div class="wishes__stage" data-reveal="rise">

                <img
                    src="{{ asset('images/lace-card.png') }}"
                    class="wishes__card-img"
                    alt=""
                    aria-hidden="true"
                >

                <div class="wishes__content">

                @if (session('wishes_success'))
                    <div class="form-alert form-alert--success" role="status" data-feedback="wishes" tabindex="-1">
                        <p class="form-alert__title">Terima kasih</p>
                        <p>{{ session('wishes_success') }}</p>
                    </div>
                @endif

                @if ($errors->wishes->any())
                    <div class="form-alert form-alert--error" role="alert" data-feedback="wishes" tabindex="-1">
                        <p class="form-alert__title">Periksa kembali formulirmu:</p>
                        <ul>
                            @foreach ($errors->wishes->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('wishes.store') }}" method="POST" class="invite-form">
                    @csrf

                    <div class="invite-form__group">
                        <label for="wishesName">Nama</label>
                        <input
                            type="text"
                            id="wishesName"
                            name="wishes[name]"
                            value="{{ old('wishes.name', request('to')) }}"
                            placeholder="Nama kamu"
                            maxlength="100"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <div class="invite-form__group">
                        <label for="wishesMessage">Ucapan dan doa</label>
                        <textarea
                            id="wishesMessage"
                            name="wishes[message]"
                            rows="4"
                            maxlength="500"
                            placeholder="Tuliskan doa terbaikmu untuk Alif &amp; Indy..."
                            required
                        >{{ old('wishes.message') }}</textarea>
                    </div>

                    <button type="submit" class="button button--submit" data-label="Kirim Ucapan">
                        <span class="button__label">Kirim Ucapan</span>
                        <svg class="icon" aria-hidden="true"><use href="#i-mail"/></svg>
                    </button>
                </form>

                </div>
            </div>

            <div class="wishes__list">
                @forelse ($wishes as $wish)
                    <article class="wish-card" data-reveal="rise">
                        <header class="wish-card__head">
                            <p class="wish-card__name">{{ $wish->name }}</p>
                            <time class="wish-card__date" datetime="{{ $wish->created_at->toIso8601String() }}">
                                {{ $wish->created_at->format('d M Y') }}
                            </time>
                        </header>
                        <p class="wish-card__message">{{ $wish->message }}</p>
                    </article>
                @empty
                    <p class="section__note">Belum ada ucapan — jadilah yang pertama menulis.</p>
                @endforelse
            </div>
        </section>

        <!-- =========================================
             CLOSING
        ========================================== -->
        <footer class="closing" id="closing">
            <img src="{{ asset('images/bunga-bawah.png') }}" class="closing__flower" alt="" aria-hidden="true">

            <p class="closing__eyebrow">With all our love</p>
            <p class="closing__names">Alif <span>&amp;</span> Indy</p>
            <p class="closing__date">28 November 2026</p>
            <p class="closing__thanks">
                Terima kasih telah menjadi bagian dari cerita kami.
                Semoga kasih dan kebahagiaan selalu menyertai kita semua.
            </p>
            <a class="closing__top" href="#ayat">Kembali ke awal</a>
            <div class="closing__bottom">Made with love · 2026</div>
        </footer>

    </main>

    <!-- =========================================
         NAVIGASI MELAYANG — penanda section aktif
    ========================================== -->
    <nav class="dock" id="dock" aria-label="Navigasi undangan">
        <a href="#ayat" data-dock="ayat"><svg class="icon" aria-hidden="true"><use href="#i-home"/></svg><span>Beranda</span></a>
        <a href="#details" data-dock="details"><svg class="icon" aria-hidden="true"><use href="#i-cal"/></svg><span>Acara</span></a>
        <a href="#moments" data-dock="moments"><svg class="icon" aria-hidden="true"><use href="#i-image"/></svg><span>Momen</span></a>
        <a href="#rsvp" data-dock="rsvp"><svg class="icon" aria-hidden="true"><use href="#i-mail"/></svg><span>RSVP</span></a>
    </nav>

    <!-- TOMBOL MUSIK (muncul jika $musicSrc diisi) -->
    @if ($musicSrc)
        <button
            type="button"
            class="music-toggle"
            id="musicToggle"
            aria-pressed="false"
            aria-label="Putar musik"
        >
            <svg class="icon" aria-hidden="true"><use href="#i-note"/></svg>
        </button>
    @endif

    {{-- MUSIK OPSIONAL --}}
    @if ($musicSrc)
        <audio
            id="bgMusic"
            src="{{ $musicSrc }}"
            loop
            preload="none"
        ></audio>
    @endif

    {{-- JAVASCRIPT --}}
    <script src="{{ asset('js/invitation.js') }}"></script>

    <script>
        /* Pengaman jika invitation.js gagal dimuat */
        setTimeout(function () {
            if (!window.__invitationReady) {
                document.documentElement.classList.replace('js', 'no-js');
            }
        }, 4000);
    </script>

</body>
</html>
