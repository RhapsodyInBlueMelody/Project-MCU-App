<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css?v=1.0')?>">
    <title>Rumah Sakit </title>
</head>
<body>
<div id="welcome-banner" class="d-flex align-items-center justify-content-between p-2 bg-info text-white">
    <div class="d-none d-md-block" id="realtime-clock">
    </div>
    <div id="welcome-text" class="text-center flex-grow-1">
        Selamat Datang
    </div>
    <div class="d-md-none" id="language-switcher-mobile">
        <div class="flag-container" id="flag-display" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="language" id="en-mobile" value="en">
            <label class="form-check-label" for="en-mobile">EN</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="language" id="id-mobile" value="id" checked>
            <label class="form-check-label" for="id-mobile">ID</label>
        </div>
    </div>
    <div class="d-none d-md-flex" id="language-switcher">
        <div class="flag-container" id="flag-display" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="language" id="en" value="en">
            <label class="form-check-label" for="en">EN</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="language" id="id" value="id" checked>
            <label class="form-check-label" for="id">ID</label>
        </div>
    </div>
</div>
<nav class="navbar navbar-expand-lg navbar-light bg-light" style="padding: 10px 20px;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" style="margin-right: 30px;">
            <div style="border: 1px solid #ccc; width: 120px; height: 40px; display: flex; justify-content: center; align-items: center; color: #777; font-size: 12px;">
                Image Placeholder
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Beranda</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRumahSakit" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Rumah Sakit Kami
                    </a>
                    <ul class="dropdown-menu animated-dropdown" aria-labelledby="navbarDropdownRumahSakit">
                        <li><a class="dropdown-item" href="#">Sub Menu 1</a></li>
                        <li><a class="dropdown-item" href="#">Sub Menu 2</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownFasilitas" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Fasilitas Kami
                    </a>
                    <ul class="dropdown-menu animated-dropdown" aria-labelledby="navbarDropdownFasilitas">
                        <li><a class="dropdown-item" href="#">Sub Menu 1</a></li>
                        <li><a class="dropdown-item" href="#">Sub Menu 2</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Cari Dokter</a>
                </li>
                <li class="nav-item d-lg-none">
                    <div id="language-switcher-nav" class="mt-2">
                        <div class="flag-container" id="flag-display-nav" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="language" id="en-nav" value="en">
                            <label class="form-check-label" for="en-nav">EN</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="language" id="id-nav" value="id" checked>
                            <label class="form-check-label" for="id-nav">ID</label>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="d-flex">
                <button class="btn btn-outline-primary btn-sm me-2" type="button" style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">Buat Janji Temu</button>
                <button class="btn btn-outline-success btn-sm" type="button" style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">Daftar/Masuk</button>
            </div>
        </div>
    </div>
</nav>
  

