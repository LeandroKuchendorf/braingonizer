/**
 * Braingonizer Dashboard Module - Frontend JavaScript
 * Handles carousel initialization for reminders
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Reminders Carousel with auto-play
    const remindersCarousel = document.querySelector('#remindersCarousel');
    if (remindersCarousel) {
        const carousel = new bootstrap.Carousel(remindersCarousel, {
            interval: 5000,  // 5 seconds between slides
            wrap: true,      // Infinite loop
            pause: 'hover',  // Pause on hover
            touch: true      // Enable touch/swipe
        });
    }
});
