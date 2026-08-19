import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * স্ক্রল করলে .reveal এলিমেন্টগুলো ফুটে ওঠে (শিরোনাম, সাবটাইটেল)।
 *
 * দুই ধাপে:
 *   ১) প্রথমে একবার মেপে নেওয়া — যেগুলো ইতিমধ্যেই পর্দায় আছে বা উপরে উঠে গেছে
 *      (অ্যাংকর লিংকে লাফ, মাঝপথে রিলোড, back বাটন) সেগুলো সাথে সাথে দেখাও।
 *      IntersectionObserver একা এগুলো ধরতে পারে না — উপরে চলে যাওয়া এলিমেন্টে
 *      সে কখনো ফায়ার করে না, ফলে লেখা চিরতরে অদৃশ্য থেকে যেত।
 *   ২) বাকিগুলোর জন্য IntersectionObserver — স্ক্রল লিসেনারের চেয়ে হালকা।
 */
function initReveal() {
    const items = Array.from(document.querySelectorAll('.reveal'));

    if (! items.length) return;

    const show = (el) => el.classList.add('is-visible');
    const line = window.innerHeight * 0.92; // নিচ থেকে একটু ভেতরে এলেই শুরু

    // ধাপ ১ — এখনই যা দেখা উচিত
    const rest = items.filter((el) => {
        if (el.getBoundingClientRect().top < line) {
            show(el);
            return false;
        }
        return true;
    });

    if (! rest.length) return;

    // ধাপ ২ — বাকিগুলো স্ক্রলে
    if (! ('IntersectionObserver' in window)) {
        rest.forEach(show); // পুরোনো ব্রাউজার — লুকিয়ে রাখার চেয়ে দেখানো ভালো
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) return;
                show(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -8% 0px' }
    );

    rest.forEach((el) => observer.observe(el));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReveal);
} else {
    initReveal();
}
