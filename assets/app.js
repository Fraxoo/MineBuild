import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('turbo:load', () => {


    document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-carousel-track]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');
        const slides = track.children;

        let currentIndex = 0;

        function updateCarousel() {
            track.style.transform = `translateX(-${currentIndex * 100}%)`;
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentIndex = currentIndex === 0
                    ? slides.length - 1
                    : currentIndex - 1;

                updateCarousel();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentIndex = currentIndex === slides.length - 1
                    ? 0
                    : currentIndex + 1;

                updateCarousel();
            });
        }
    });
    
    document.querySelectorAll('[data-share-button]').forEach((button) => {
        button.addEventListener('click', async () => {
            const title = button.dataset.shareTitle;
            const url = button.dataset.shareUrl;

            if (navigator.share) {
                await navigator.share({
                    title: title,
                    url: url,
                });

                return;
            }

            await navigator.clipboard.writeText(url);

            const text = button.querySelector('span');
            const oldText = text.textContent;

            text.textContent = 'Lien copié';

            setTimeout(() => {
                text.textContent = oldText;
            }, 1500);
        });
    });
})

