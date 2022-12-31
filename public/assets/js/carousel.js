window.onload = function () {
    const carousel = document.querySelector(".carousel");
    const slides = carousel.querySelectorAll(".slide");
    let currentSlide = 0;

    function showSlide(n) {
        slides.forEach((slide) => slide.classList.add("hidden"));
        slides[n].classList.remove("hidden");
    }

    document.querySelector(".prev").addEventListener("click", () => {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    });

    document.querySelector(".next").addEventListener("click", () => {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    });

    showSlide(currentSlide);
};
