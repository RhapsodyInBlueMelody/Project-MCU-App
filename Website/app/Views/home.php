<!-- Enhanced Homepage Content -->
<main class="min-h-screen bg-gray-50">
    <!-- Hero Section with Enhanced Carousel -->
    <section class="relative">
        <div x-data="{
            currentIndex: 0,
            images: [
                {
                    url: 'https://media.istockphoto.com/id/1159883458/vector/medical-insurance-template-hospital.jpg?s=612x612&w=0&k=20&c=EdgxXlcCy6IQRLJk6fTvA1Aa-NE1J97mCxm3lVI_W5s=',
                    title: 'Layanan Medical Check Up Terpercaya',
                    subtitle: 'Pemeriksaan kesehatan komprehensif dengan teknologi terdepan'
                },
                {
                    url: 'https://media.istockphoto.com/id/1298883368/photo/modern-hospital-isolation-rooms.jpg?s=612x612&w=0&k=20&c=4dbZdrRS87cgnIBVe8yii5_1F-NcKtiLTcQDPE-yiLM=',
                    title: 'Fasilitas Modern & Steril',
                    subtitle: 'Ruangan berstandar internasional untuk kenyamanan Anda'
                },
                {
                    url: 'https://media.istockphoto.com/id/1295775470/photo/hospital-corridor.jpg?s=612x612&w=0&k=20&c=EzhDKzSGsvzAkKcpraz92HSl776Ub2yAVe6kM08LTj4=',
                    title: 'Tim Medis Berpengalaman',
                    subtitle: 'Dokter spesialis dan tenaga medis profesional siap melayani'
                }
            ],
            next() {
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
            },
            prev() {
                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            },
            goToSlide(index) {
                this.currentIndex = index;
            }
        }" x-init="setInterval(() => next(), 5000)" class="relative overflow-hidden">
            
            <!-- Carousel Container -->
            <div class="relative h-96 md:h-[500px]">
                <template x-for="(image, index) in images" :key="index">
                    <div x-show="currentIndex === index" 
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 transform translate-x-full"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         x-transition:leave="transition ease-in duration-500"
                         x-transition:leave-start="opacity-100 transform translate-x-0"
                         x-transition:leave-end="opacity-0 transform -translate-x-full"
                         class="absolute inset-0">
                        
                        <!-- Background Image -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-purple-900/70"></div>
                        <img :src="image.url" :alt="image.title" class="w-full h-full object-cover">
                        
                        <!-- Overlay Content -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center text-white px-6 max-w-4xl">
                                <h1 x-text="image.title" class="text-3xl md:text-5xl font-bold mb-4 animate-fade-in-up"></h1>
                                <p x-text="image.subtitle" class="text-lg md:text-xl mb-8 animate-fade-in-up animation-delay-200"></p>
                                <div class="space-x-4 animate-fade-in-up animation-delay-400">
                                    <a href="#book-appointment" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                                        Buat Janji Sekarang
                                    </a>
                                    <a href="#services" class="inline-block bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold py-3 px-8 rounded-full transition duration-300">
                                        Lihat Layanan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Navigation Arrows -->
                <button @click="prev()" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-full p-3 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button @click="next()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-full p-3 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                
                <!-- Dots Indicator -->
                <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3">
                    <template x-for="(image, index) in images" :key="index">
                        <button @click="goToSlide(index)" 
                                class="w-3 h-3 rounded-full transition duration-300"
                                :class="currentIndex === index ? 'bg-white' : 'bg-white/50 hover:bg-white/75'">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Info Cards -->
    <section class="py-12 -mt-16 relative z-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Medical Check Up</h3>
                    <p class="text-gray-600">Pemeriksaan kesehatan menyeluruh dengan paket lengkap</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Buka 24 Jam</h3>
                    <p class="text-gray-600">Layanan kesehatan tersedia setiap saat untuk Anda</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Tim Profesional</h3>
                    <p class="text-gray-600">Dokter spesialis dan tenaga medis berpengalaman</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Hospital Section -->
    <section id="about" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">RSUD Persahabatan</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mb-8"></div>
                <p class="text-lg text-gray-600 leading-relaxed mb-8">
                    RSUD Persahabatan adalah rumah sakit rujukan nasional untuk layanan paru dan pernapasan, 
                    dengan komitmen memberikan layanan kesehatan terbaik di Jakarta Timur. Kami telah melayani 
                    masyarakat dengan dedikasi tinggi dan teknologi medis terdepan.
                </p>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-12">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-2">25+</div>
                        <div class="text-gray-600">Tahun Pengalaman</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-2">100+</div>
                        <div class="text-gray-600">Dokter Spesialis</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-2">50K+</div>
                        <div class="text-gray-600">Pasien Dilayani</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-2">24/7</div>
                        <div class="text-gray-600">Layanan Darurat</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Layanan Medical Check Up</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mb-6"></div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kami menyediakan berbagai paket medical check up yang disesuaikan dengan kebutuhan Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Basic Package -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Paket Basic</h3>
                        <p class="text-gray-600 mb-4">Pemeriksaan dasar untuk kesehatan umum</p>
                        <ul class="text-sm text-gray-600 space-y-2 mb-6">
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Pemeriksaan fisik</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Cek darah lengkap</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Rontgen thorax</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>EKG</li>
                        </ul>
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                            Pilih Paket
                        </button>
                    </div>
                </div>
                
                <!-- Premium Package -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1 relative">
                    <div class="absolute top-0 right-0 bg-green-500 text-white px-3 py-1 text-sm font-semibold rounded-bl-lg">
                        Populer
                    </div>
                    <div class="p-6">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Paket Premium</h3>
                        <p class="text-gray-600 mb-4">Pemeriksaan lengkap untuk deteksi dini</p>
                        <ul class="text-sm text-gray-600 space-y-2 mb-6">
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Semua dari paket Basic</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>USG abdomen</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Tes fungsi hati</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Konsultasi dokter spesialis</li>
                        </ul>
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                            Pilih Paket
                        </button>
                    </div>
                </div>
                
                <!-- Executive Package -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Paket Executive</h3>
                        <p class="text-gray-600 mb-4">Pemeriksaan komprehensif terlengkap</p>
                        <ul class="text-sm text-gray-600 space-y-2 mb-6">
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Semua dari paket Premium</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>CT Scan</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Treadmill test</li>
                            <li class="flex items-center"><span class="text-green-500 mr-2">✓</span>Konsultasi multi spesialis</li>
                        </ul>
                        <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                            Pilih Paket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="book-appointment" class="py-16 bg-blue-600">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Siap untuk Medical Check Up?</h2>
                <p class="text-xl text-blue-100 mb-8">
                    Jangan tunda kesehatan Anda. Buat janji sekarang dan dapatkan pelayanan terbaik dari tim medis profesional kami.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="https://web.whatsapp.com/" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold py-4 px-8 rounded-full shadow-lg transition duration-300 transform hover:scale-105 space-x-3 min-w-[250px]">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.106"/>
                        </svg>
                        <span>Hubungi via WhatsApp</span>
                    </a>
                    <button class="bg-white hover:bg-gray-100 text-blue-600 font-semibold py-4 px-8 rounded-full shadow-lg transition duration-300 transform hover:scale-105 min-w-[200px]">
                        Telepon Sekarang
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Custom CSS -->
<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.8s ease-out forwards;
    }
    
    .animation-delay-200 {
        animation-delay: 0.2s;
        opacity: 0;
    }
    
    .animation-delay-400 {
        animation-delay: 0.4s;
        opacity: 0;
    }
    
    .hover-scale {
        transition: transform 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
    }
    
    /* Smooth scrolling for anchor links */
    html {
        scroll-behavior: smooth;
    }
    
    /* Custom gradient backgrounds */
    .gradient-blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .gradient-green {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
</style>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll('.hover-scale, .bg-white');
    animateElements.forEach(el => observer.observe(el));
});
</script>
