<div x-data="{
    currentIndex: 0,
    desktopImages: [
        'https://placehold.co/1920x512/333/fff?text=Desktop+Slide+1',
        'https://placehold.co/1920x512/007bff/fff?text=Desktop+Slide+2',
        'https://placehold.co/1920x512/28a745/fff?text=Desktop+Slide+3'
    ],
    mobileImages: [
        'https://placehold.co/640x360/333/fff?text=Mobile+Slide+1',
        'https://placehold.co/640x360/007bff/fff?text=Mobile+Slide+2',
        'https://placehold.co/640x360/28a745/fff?text=Mobile+Slide+3'
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
                :class="{ 'opacity-100': currentIndex === index, 'opacity-0': currentIndex !== index }"
            >
                <img :src="image" alt="Carousel Slide" class="w-full h-full object-cover">
            </div>
        </template>

        <button @click="prev()" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="next()" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <template x-for="(image, index) in currentImages" :key="index">
                <button
                    @click="dotClick(index)"
                    class="w-3 h-3 rounded-full focus:outline-none"
                    :class="{ 'bg-blue-500': currentIndex === index, 'bg-gray-300': currentIndex !== index }"
                ></button>
            </template>
        </div>
    </div>

    <h1 class="text-2xl font-bold mt-4">RSUD Persahabatan</h1>
    <p></p>
</div>