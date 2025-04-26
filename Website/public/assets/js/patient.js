const toggleMobileMenuButton = document.getElementById('toggleMobileMenu');
        const mobileMenu = document.getElementById('mobileMenu');
        const toggleRumahSakitButton = document.getElementById('toggleRumahSakitMobile');
        const dropdownRumahSakit = document.getElementById('dropdownRumahSakitMobile');
        const toggleFasilitasButton = document.getElementById('toggleFasilitasMobile');
        const dropdownFasilitas = document.getElementById('dropdownFasilitasMobile');

        if (toggleMobileMenuButton && mobileMenu) {
            toggleMobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        if (toggleRumahSakitButton && dropdownRumahSakit) {
            toggleRumahSakitButton.addEventListener('click', () => {
                dropdownRumahSakit.classList.toggle('hidden');
            });
        }

        if (toggleFasilitasButton && dropdownFasilitas) {
            toggleFasilitasButton.addEventListener('click', () => {
                dropdownFasilitas.classList.toggle('hidden');
            });
        }