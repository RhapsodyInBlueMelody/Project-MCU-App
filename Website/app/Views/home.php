<div x-data="{
    currentIndex: 0,
    desktopImages: [
        'https://media.istockphoto.com/id/1159883458/vector/medical-insurance-template-hospital.jpg?s=612x612&w=0&k=20&c=EdgxXlcCy6IQRLJk6fTvA1Aa-NE1J97mCxm3lVI_W5s=',
        'https://media.istockphoto.com/id/1298883368/photo/modern-hospital-isolation-rooms.jpg?s=612x612&w=0&k=20&c=4dbZdrRS87cgnIBVe8yii5_1F-NcKtiLTcQDPE-yiLM=',
        'https://media.istockphoto.com/id/1295775470/photo/hospital-corridor.jpg?s=612x612&w=0&k=20&c=EzhDKzSGsvzAkKcpraz92HSl776Ub2yAVe6kM08LTj4='
    ],
    mobileImages: [
        'https://media.istockphoto.com/id/1159883458/vector/medical-insurance-template-hospital.jpg?s=612x612&w=0&k=20&c=EdgxXlcCy6IQRLJk6fTvA1Aa-NE1J97mCxm3lVI_W5s=',
        'https://media.istockphoto.com/id/1298883368/photo/modern-hospital-isolation-rooms.jpg?s=612x612&w=0&k=20&c=4dbZdrRS87cgnIBVe8yii5_1F-NcKtiLTcQDPE-yiLM=',
        'https://media.istockphoto.com/id/1295775470/photo/hospital-corridor.jpg?s=612x612&w=0&k=20&c=EzhDKzSGsvzAkKcpraz92HSl776Ub2yAVe6kM08LTj4='
    ],
    get currentImages() {
        return window.innerWidth >= 640 ? this.desktopImages : this.mobileImages;
    },
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.currentImages.length;
    },
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.currentImages.length) % this.currentImages.length;
    },
    dotClick(index) {
        this.currentIndex = index;
    },
    autoSlide() {
        setInterval(() => {
            this.next();
        }, 10000);
    }
}" x-init="autoSlide()" class="relative w-full overflow-hidden rounded-md">
    <div class="relative md:h-96 h-64"> <template x-for="(image, index) in currentImages" :key="index">
            <div
                x-show="currentIndex === index"
                class="absolute top-0 left-0 w-full h-full transition-opacity duration-500"
                :class="{ 'opacity-100': currentIndex === index, 'opacity-0': currentIndex !== index }">
                <img :src="image" alt="Carousel Slide" class="w-full h-full object-cover">
            </div>
        </template>

        <button @click="prev()" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button @click="next()" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <template x-for="(image, index) in currentImages" :key="index">
                <button
                    @click="dotClick(index)"
                    class="w-3 h-3 rounded-full focus:outline-none"
                    :class="{ 'bg-blue-500': currentIndex === index, 'bg-gray-300': currentIndex !== index }"></button>
            </template>
        </div>
    </div>
    <div class="text-center mt-8 px-4 content-center">
        <div class="bg-gray-100 py-8 px-6 rounded-lg max-w-3xl mx-auto shadow-md">
            <h1 class="text-3xl font-bold text-black-800">RSUD Persahabatan</h1>
            <p class="mt-4 text-gray-700">
                RSUD Persahabatan adalah rumah sakit rujukan nasional untuk layanan paru dan pernapasan,
                dengan komitmen memberikan layanan kesehatan terbaik di Jakarta Timur.
            </p>
            <div class="flex justify-center">
                <a href="https://web.whatsapp.com/" target="_blank" rel="noopener noreferrer">
                    <button class="mt-6 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-full shadow-lg transition duration-300 flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-white">
                            <path fill-rule="evenodd" d="M4.876 9.69A5.986 5.986 0 0112 8a5.986 5.986 0 017.124 6.622A6.027 6.027 0 0112 16a5.976 5.976 0 01-6.624-5.374A3.35 3.35 0 004 12a3.346 3.346 0 001.255-2.31z" clip-rule="evenodd" />
                        </svg>
                        <span>Hubungi Kami via WhatsApp</span>
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>